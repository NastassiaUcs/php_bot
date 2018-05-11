<?php
define('DIRECTORY', 'commands');


require_once __DIR__ . '/' . DIRECTORY . '/_base_command.php';
require_once __DIR__ . '/telegram.php';

class Bot {
    private $config;
    /* @var Telegram */
    private $tg;

    public function __construct($config) {
        $this->config = $config;
        $this->tg = new Telegram($config['token']);
    }

    public function loadCommand($commandName) {
        $className = $commandName . "_command";
        $filePath = __DIR__ . '/' . DIRECTORY . '/' . $commandName . ".php";

        if (file_exists($filePath)) {
            require_once $filePath;
            if (class_exists($className)) {
                return new $className(array('allowed_ids' => $this->config['allowed_ids']), $commandName, $this->tg);
            } else {
                throw new Exception("not found class $className in $filePath");
            }
        } else {
            return false;
        }
    }

    private function parseText($text) {
        $result = array(
            'text' => $text,
            'is_command' => false
        );
        if ($text[0] == '/') {
            $parts = explode(' ', $text);
            $cmdName = substr($parts[0], 1);
            $result['is_command'] = true;
            $result['command_name'] = $cmdName;
        }
        return $result;
    }

    public function processPacket($packet) {
        print_r($packet);
        if (isset($packet['message'])) {
            $text = $packet['message']['text'];
            $chatId = $packet['message']['chat']['id'];
            $userId = $packet['message']['from']['id'];
            $data = $this->parseText($text);
            if ($data['is_command']) {
                $cmd = $this->loadCommand($data['command_name']);
                if ($cmd !== false && $cmd instanceof BaseCommand && ($cmd->hasAccess($chatId) || $cmd->hasAccess($userId))) {
                    $result = $cmd->process($chatId, $text, $userId, $packet);
                    if(is_array($result)){
                        $this->tg->{$result["type"]}($chatId, $result["data"]);
                    }elseif(!empty($return)){
                        $this->tg->sendMessage($chatId, $result);
                    }
                }
            }
        }
    }

    public function startPolling() {
        $offset = 0;
        while (true) {
            $updates = $this->tg->getUpdates($offset);
            if ($updates && count($updates) > 0) {
                $offset = $updates[count($updates) - 1]['update_id'] + 1;
            }
            foreach ($updates as $update) {
                $this->processPacket($update);
            }
            usleep($this->config['interval'] * 1000); //micro seconds, not milli seconds
        }
    }
}

$config = json_decode(file_get_contents('config.json'), true);
$bot = new Bot($config);
if (!$config['webhook']) {
    $bot->startPolling();
}

?>