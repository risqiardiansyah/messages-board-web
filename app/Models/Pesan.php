<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pesan extends Model
{
    use HasFactory;
    protected $table = 'pesan';

    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'id_user');
    }
}
