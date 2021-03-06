<?php
define("DIR_BOT_ROOT", __DIR__. "/");
define("DIR_COMMANDS", DIR_BOT_ROOT . "commands/");


require_once DIR_COMMANDS . '_base_command.php';
require_once DIR_BOT_ROOT . '/telegram.php';

class Bot {
    private $config;
    /* @var Telegram */
    private $tg;
    private $aliases = [];

    public function __construct($config) {
        $this->config = $config;
        $this->tg = new Telegram($config['token']);
        $this->bot = $this->tg->getMe();
        if(empty($this->bot["username"])) exit("Не верный токен\r\n");

        if(file_exists(DIR_BOT_ROOT."aliases.json")){
            $this->aliases = json_decode(file_get_contents(DIR_BOT_ROOT."aliases.json"),true);
        }
    }

    public function loadCommand($commandName) {
        $className = $commandName . "_command";
        $filePath = DIR_COMMANDS . $commandName . ".php";

        if (file_exists($filePath)) {
            require_once $filePath;
            if (class_exists($className)) {
                return new $className(['allowed_ids' => $this->config['allowed_ids']], $commandName, $this->tg);
            }
        }
        return false;
    }

    private function parseText($text) {
        $result = array(
            'text' => $text,
            'is_command' => false
        );
        if ($text[0] == '/') {
            $parts = explode(' ', $text);
            $cmdName = str_replace("@".$this->bot["username"],"", substr($parts[0], 1)); // временно... для работы команды /команда@имя_бота
            $result['is_command'] = true;
            $result['command_name'] = $cmdName;
        }elseif(!empty($this->aliases)){
			$text = mb_strtolower(trim($text));
        	if(array_key_exists($text, $this->aliases)){
	            $result['is_command'] = true;
	            $result['command_name'] = $this->aliases[$text];
        	}
        }
        return $result;
    }

    public function processPacket($packet){
        print_r($packet);
        if (isset($packet['message']['text'])) {
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
                    }elseif(!empty($result)){
                        $this->tg->sendMessage($chatId, $result);
                    }
                    $this->tg->deleteMessage($chatId,$packet['message']["message_id"]);
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

$config = json_decode(file_get_contents(DIR_BOT_ROOT . "config.json"), true);
$bot = new Bot($config);

if (!$config['webhook']) {
    $bot->startPolling();
}

?>