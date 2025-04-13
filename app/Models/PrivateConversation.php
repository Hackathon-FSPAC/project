<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateConversation extends Model
{
    use HasFactory;

    protected $fillable = ['user1_id', 'user2_id'];

    // Adaugă aceste relații
    public function user1()
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    public function messages()
    {
        return $this->hasMany(PrivateMessage::class, 'conversation_id');
    }

    public function otherUser(User $user)
    {
        return $this->user1_id === $user->id ? $this->user2 : $this->user1;
    }
}