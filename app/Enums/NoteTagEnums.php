<?php

namespace App\Enums;

enum NoteTagEnums: string
{
    case Personal = 'personal';
    case Work = 'work';
    case Knowledge = 'knowledge';
    case Finance = 'finance';
    case Other = 'other';
}
