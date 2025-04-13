<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateMessage extends Model
{
    use HasFactory;

    protected $fillable = ['conversation_id', 'sender_id', 'content', 'read'];

    public function conversation()
    {
        return $this->belongsTo(PrivateConversation::class, 'conversation_id');
        // Sau 'private_conversation_id' dacă ai ales să păstrezi acest nume
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}