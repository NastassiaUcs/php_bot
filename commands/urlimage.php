<?php
require_once __DIR__ . '/_base_command.php';

class urlimage_command extends BaseCommand {

    function process($chatId, $text, $userId, $packet)
    {
        return ["type"=>"sendPhoto", "data"=> "https://cdn0.froala.com/assets/editor/docs/server/meta-social/php-e8c6425acd65e1cbc012639ad25598c7.png"];
    }
}