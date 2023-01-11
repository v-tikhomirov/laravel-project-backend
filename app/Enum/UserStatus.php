<?php

namespace App\Enum;

enum UserStatus: string
{
    case ADMIN = 'admin';
    case COMPANY = 'company';
    case USER = 'user';
}
