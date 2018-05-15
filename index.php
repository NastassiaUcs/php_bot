<?php
require_once __DIR__ . '/bot.php';

try{
    $data = file_get_contents('php://input');
    $data = json_decode($data, true);
    $bot->processPacket($data);
} catch(\Exception $e){
    echo "Поизошла ошибка: {$e->getMessage()}" . PHP_EOL .
    var_export($data, true);
}
