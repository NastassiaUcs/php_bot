<?php
require_once __DIR__ . '/_base_command.php';

class remove_command extends BaseCommand {

    function process($chatId, $text, $userId, $packet)
    {
    	if(isset($packet["message"]["reply_to_message"])){
    		return ["type"=>"deleteMessage", "data"=>$packet["message"]["reply_to_message"]["message_id"]];
    	}
    }
}