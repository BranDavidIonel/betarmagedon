<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    use HasFactory;
    protected $table = 'competitions';
    protected $fillable = [
        'id',
        'name',
        'country_id',
        'alias'
    ];
    public function linksSearchPages()
    {
        return $this->hasMany(LinksSearchPage::class, 'competition_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

}
