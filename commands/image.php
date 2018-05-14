<?php
require_once __DIR__ . '/_base_command.php';

class image_command extends BaseCommand {

    function process($chatId, $text, $userId, $packet)
    {
        $dir = DIR_BOT_ROOT . "example/images/";
        $images = array_values(array_diff(scandir($dir), [".",".."]));
        return ["type"=>"sendPhoto", "data"=> $dir . $images[rand(0,count($images)-1)]];
    }
}