<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealNote extends Model
{
    // Libera os campos para serem salvos
    protected $fillable = [
        'deal_id',
        'user_id',
        'note',
        'attachment_path',
    ];

    // Diz ao sistema que esta anotação pertence a uma Negociação
    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    // Diz ao sistema que esta anotação foi escrita por um Usuário
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
