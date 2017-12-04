<?php

require_once __DIR__ . '/_base_command.php';

class getid_command extends BaseCommand {

    function process($chatId, $text, $userId, $packet)
    {
        return $packet['message']['from']['id'];
    }
}