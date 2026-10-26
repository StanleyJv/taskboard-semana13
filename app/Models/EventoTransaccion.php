<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventoTransaccion extends Model
{
    use HasFactory;

    protected $table = 'eventos_transaccion';

    protected $fillable = [
        'transaccion_id',
        'estado_anterior',
        'estado_nuevo',
    ];

    public function transaccion(): BelongsTo
    {
        return $this->belongsTo(Transaccion::class);
    }
}