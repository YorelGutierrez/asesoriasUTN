<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'fecha_nacimiento' => 'date',
    ];

    // ROLES
    const ROL_ADMIN = 'admin';
    const ROL_USUARIO = 'usuario';
    const ROL_ALUMNO = 'alumno';
    const ROL_TUTOR = 'tutor';
    const ROL_DOCENTE = 'docente';

    // Métodos helper
    public function esAdmin()
    {
        return $this->rol === self::ROL_ADMIN;
    }

    public function esUsuario()
    {
        return $this->rol === self::ROL_USUARIO;
    }

    public function getFotoUrlAttribute()
    {
        if ($this->foto_perfil) {
            return asset('storage/' . $this->foto_perfil);
        }
        return asset('img/default-avatar.png');
    }

    // Relación con docente (un usuario puede ser docente)
    public function docente()
    {
        return $this->hasOne(docentes::class);
    }

    // TOKEN PARA CONSULTAS Y APIS
    public function getJWTIdentifier() //JSON WEN TOKEN
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
