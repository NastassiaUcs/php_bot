<?php

require_once __DIR__ . '/_base_command.php';

class getid_command extends BaseCommand {

    function process($chatId, $text, $userId, $packet)
    {
        // return $packet['message']['from']['id']; // Для отправки обычных сообщений можно возвращать строку (оставлено для совместимости). Для других методов нужно возвращать массив вида ["type"=> "название метода", "data"=>"второй параметр метода"]
        return ["type"=>"sendMessage", "data"=>$packet['message']['from']['id']];
    }
}