<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
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
        'name',
        'email',
        'password',
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
    ];

    public static function createUser($login, $password)
    {
        $user = new User();
        $user->login = $login;
        $user->password = Hash::make($password);
        $user->save();
    }

    public static function check($login, $password):bool
    {
        $user = User::where('login', $login)->first();

        return $user && Hash::check($password, $user->password);
    }

    public function shippings()
    {
        return $this->hasMany(Shipping::class, 'user_id');
    }

    public static function getUser($api_token)
    {
        return User::where('token', $api_token)->first();
    }
}
