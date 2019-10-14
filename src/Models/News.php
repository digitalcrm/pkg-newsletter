<?php

namespace Digitalcrm\Newsletter\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table = 'News';  
    protected $fillable = array('email');
    //protected $guarded = [];
}
