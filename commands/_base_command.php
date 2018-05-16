<?php

abstract class BaseCommand {
    protected $name;
    protected $allowedIds;
    /* @var Telegram */
    private $tg;

    public function __construct($data, $name, $tg)
    {
        $this->name = $name;
        $this->allowedIds = $data['allowed_ids'];
    }

    public function getName() {
        return $this->name;
    }

    public function hasAccess($id) {
        return in_array($id, $this->allowedIds) || empty($this->allowedIds);
    }

    abstract function process($chatId, $text, $userId, $packet);
}