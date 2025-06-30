<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Arac extends Model
{
    protected $table = 'araclar';
    protected $fillable = ['ad', 'yakit_tuketimi'];
}
