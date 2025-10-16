<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'target_id',
        'target_type',
        'category',
        'severity',
        'description',
        'attachment',
        'contact_email',
        'status',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
