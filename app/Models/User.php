<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'user';
    protected $primaryKey = 'id';

    public function posts(): HasMany
    {
        return $this->hasMany(Pesan::class, 'id_user');
    }
}
