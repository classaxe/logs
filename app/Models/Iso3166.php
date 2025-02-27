<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iso3166 extends Model
{
    use HasFactory;

    protected $table = 'iso3166';

    public static function getOptions()
    {
        return Iso3166::select()->orderBy('country', 'asc');
    }
}
