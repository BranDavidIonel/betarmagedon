<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SitesSearch extends Model
{
    use HasFactory;

    protected $table = 'sites_search';

    // Atributele care pot fi completate prin atribuire în masă
    protected $fillable = [
        'name',
        'link_home_page'
    ];
}
