<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Iso3166 extends Model
{
    public static function getOptions()
    {
        return Iso3166::select()->orderBy('country', 'asc');
    }
}
