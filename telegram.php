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

    private function request($tgMethod, $params = array()) {
        $url = $this->url . $tgMethod;
        $ch = curl_init($url);
        $params = http_build_query($params);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);

        $data = json_decode($response, true);
        if ($data['ok']) {
            return $data['result'];
        }
        return $data;
    }
}