<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $table = 'countries';

    protected $fillable = [
        'id',
        'name'
    ];
    public function competitions()
    {
        return $this->hasMany(Competition::class, 'country_id');
    }
}
