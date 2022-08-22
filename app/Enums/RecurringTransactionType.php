<?php

namespace App\Enums;

enum RecurringTransactionType: int
{
    case SINGLE = 1;
    case GROUP = 2;
}
