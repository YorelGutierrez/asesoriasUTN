<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'email',
        'password',
        'fecha_nacimiento',
        'telefono',
        'foto_perfil',
        'rol',
        'estado',   // ← IMPORTANTE para bloqueo
    ];

    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'fecha_nacimiento' => 'date',
    ];

    const ROL_ADMIN = 'admin';
    const ROL_DOCENTE = 'docente';
    const ROL_ALUMNO = 'alumno';

    // Relación con docente (un usuario puede ser docente)
    public function docente()
    {
        return $this->hasOne(docentes::class, 'user_id');
    }

    // Relación con alumno (un usuario puede ser alumno)
    public function alumno()
    {
        return $this->hasOne(alumnos::class, 'user_id');
    }
    // JWT
    public function getJWTIdentifier() { return $this->getKey(); }
    public function getJWTCustomClaims() { return []; }
}