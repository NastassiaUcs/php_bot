<?php

require_once __DIR__ . '/bot.php';

$data = file_get_contents('php://input');
$data = json_decode($data, true);
$bot->processPacket($data);
