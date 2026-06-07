<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalQuestion extends Model
{
    protected $fillable = [
        'question',
        'answer_html',
        'answer_text',
        'category',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
