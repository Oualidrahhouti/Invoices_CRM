<?php

namespace App\Model;

enum StatusEnum: string
{
    case PAID = 'paid';
    case SENT = 'sent';
    case CANCELED = 'canceled';
}