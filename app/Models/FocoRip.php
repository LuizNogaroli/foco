<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FocoRip extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'foco_rips';

    protected $fillable = [
        'foco_id',
        'numero_rip',
        'destinacao_terreno',
        'area_terreno_parcial',
        'destinacao_imovel',
        'area_imovel_parcial',
    ];

    public function foco()
    {
        return $this->belongsTo(Foco::class);
    }
}
