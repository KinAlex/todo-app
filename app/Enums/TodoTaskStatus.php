<?php

namespace App\Enums;

enum TodoTaskStatus: string
{
    case New = 'Новый';
    case Processing = 'В работе';
    case Done = 'Готово';
}
