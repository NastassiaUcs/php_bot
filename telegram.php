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

    /**
     * Метод отправки файлов/документов
     * @param  [int]  $chatId     [ID чата]
     * @param  [string] $pathToFile [ПОЛНЫЙ ПУТЬ к файлу от корня]
     * @return [type]             [description]
     */
    public function sendDocument($chatId, $pathToFile){
        return $this->request('sendDocument', ["chat_id" => $chatId, "document" => new CURLFile(realpath($pathToFile))]);
    }

    /**
     * Метод отправки изображений/фотографий
     * @param  [int]  $chatId     [ID чата]
     * @param  [string] $pathToFile [ПОЛНЫЙ ПУТЬ к файлу картинки от корня]
     * @return [type]             [description]
     */
    public function sendPhoto($chatId, $pathToFile){
        return $this->request('sendPhoto', ["chat_id" => $chatId, "photo" => new CURLFile(realpath($pathToFile))]);
    }

    private function request($tgMethod, $params = array()) {
        $url = $this->url . $tgMethod;
        $ch = curl_init($url);
        // $params = http_build_query($params);
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