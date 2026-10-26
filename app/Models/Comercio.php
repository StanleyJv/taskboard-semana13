<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Comercio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre_comercio',
        'rubro',
        'fecha_afiliacion',
        'user_id',
    ];

    public function transacciones(): HasMany
    {
        return $this->hasMany(Transaccion::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}