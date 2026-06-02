<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';
    public $timestamps = false;

    protected $fillable = [
        'sender_id', 'receiver_id', 'subject', 'body', 'is_read', 'sent_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id_number');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id_number');
    }
}
