<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SitesSearch extends Model
{
    use HasFactory;

    protected $table = 'sites_search';

    protected $fillable = [
        'id',
        'name',
        'link_home_page'
    ];
}
