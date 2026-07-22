<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class logs extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'accion',
        'descripcion',
        'modulo',
        'ip'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}