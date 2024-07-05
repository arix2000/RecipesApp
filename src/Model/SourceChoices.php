<?php

namespace App\Model;

enum SourceChoices: string
{
    case WEB = 'fromWebsite';
    case BOOK = 'fromBook';
    case OWN = 'userOwn';
}