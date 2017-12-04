<?php


class Telegram {
    const URL_BASE = 'https://api.telegram.org/bot';

    private $token;
    private $url;

    public function __construct($token) {
        $this->token = $token;
        $this->url = self::URL_BASE . $token . '/';
    }

    public function getUpdates($offset) {
        return $this->request('getUpdates', array('offset' => $offset));
    }

    public function sendMessage($chatId, $text) {
        return $this->request('sendMessage', array('chat_id' => $chatId, 'text' => $text));
    }

    private function request($tgMethod, $data = array()) {
        $url = $this->url . $tgMethod;
        //https://stackoverflow.com/a/6609181/2940171
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === false) {
            throw new Exception ("error while requesting tg api");
        }
        $data = json_decode($result, true);
        if ($data['ok']) {
            return $data['result'];
        }
        return $data;
    }
}
