<?php

namespace App\Enums;
/**
 * Summary of DataStatus
 */
enum DataStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Deleted = 'deleted';
}
