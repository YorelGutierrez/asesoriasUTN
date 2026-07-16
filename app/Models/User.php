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
        'estado',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'fecha_nacimiento' => 'date',
    ];

    const ROL_ADMIN = 'admin';
    const ROL_DOCENTE = 'docente';
    const ROL_ALUMNO = 'alumno';

    // Relación con docente
    public function docente()
    {
        return $this->hasOne(docentes::class, 'user_id');
    }

    // Relación con alumno
    public function alumno()
    {
        return $this->hasOne(alumnos::class, 'user_id');
    }

    // JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // Accesor para obtener la URL de la foto de perfil
    public function getFotoUrlAttribute()
    {
        if ($this->foto_perfil) {
            return asset('storage/' . $this->foto_perfil);
        }
        // Generar avatar con iniciales
        $nombre = urlencode($this->nombres . '+' . $this->apellido_paterno);
        return "https://ui-avatars.com/api/?name={$nombre}&background=0d6efd&color=fff";
    }

    public function materias()
    {
        return $this->belongsToMany(materias::class, 'docente_materias', 'docente_id', 'materia_id')
            ->withTimestamps();
    }

    public function grupos()
    {
        return $this->belongsToMany(grupos::class, 'docente_grupos', 'docente_id', 'grupo_id')
            ->withTimestamps();
    }
}
