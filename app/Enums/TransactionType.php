<?php

namespace App\Enums;

enum TransactionType: int
{
    case INCOME = 1;
    case EXPENDITURE = 2;
}