<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $table = 'announcements';
    public $timestamps = false;

    protected $fillable = ['title', 'body', 'posted_by', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by', 'id_number');
    }
}
