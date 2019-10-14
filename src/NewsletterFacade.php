<?php

namespace Digitalcrm\Newsletter;

use Illuminate\Support\Facades\Facade;

class NewsletterFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'newsletter';
    }
}
