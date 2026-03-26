<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
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
        'nickname',
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

    // Definir constantes de roles
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
        return asset('img/default-avatar.png'); // Imagen por defecto (crea una en public/img)
    }
}
