<?php

namespace App\Modules\Telegram;

class TelegramMessage
{

    public function __construct(...$params)
    {
    }

    public function message(): string
    {
        $message =
            '- Ф.И.О: ' . $this->obj->second_name
            . ' ' . $this->obj->first_name
            . ' ' . $this->obj->third_name . "\n"
            . '- Организация: ' . (isset($obj->organization) ? $obj->organization : "-") . "\n"
            . '- Процент совпадения:' . round(((float)$obj->sim) * 100) . '%.' . "\n"
            . ' - Тип Операции: ' . $this->obj->operationType . "\n"
            . '- (Запрошен: ' . $this->obj->requestedInitials . ').' . "\n"
            . '- нет client id';

        return $message;
    }
}
