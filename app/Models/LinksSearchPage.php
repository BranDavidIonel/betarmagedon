<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class LinksSearchPage extends Model
{
    use HasFactory;

    protected $table = 'links_search_page';

    // Atributele care pot fi completate prin atribuire în masă
    protected $fillable = [
        'id',
        'site_id',
        'type_game',
        'link_league',
        'with_data',
        'competition_id'
    ];
    public function competition()
    {
        return $this->belongsTo(Competition::class, 'competition_id');
    }
}
