<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\MustVerifyEmail;


class User extends Authenticatable implements MustVerifyEmail
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
        'verified',
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
        'password' => 'hashed',
    ];
    // public function generatePasswordResetCode()
    // {
    //     return str_pad(mt_rand(1000, 9999), 4, '0', STR_PAD_LEFT);
    // }

    public function generatePasswordResetCode()
    {
        $randomNumber = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT); // Generate a random four-digit number

        $this->password_reset_code = $randomNumber;
        $this->password_reset_expires_at = now()->addMinutes(10);
        $this->save();
    }

    public function generatePasswordResentCode()
    {
        $randomNumber = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT); // Generate a random four-digit number

        $this->password_reset_code = $randomNumber;
        $this->password_reset_expires_at = now()->addMinutes(10);
        $this->save();

        return $randomNumber; // Return the generated code
    }

    public function transactionPin()
    {
        return $this->hasOne(TransactionPin::class);
    }
    public function beneficiaries()
    {
        return $this->hasMany(Beneficiary::class);
    }

}
