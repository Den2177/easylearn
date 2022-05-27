<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    protected $guarded = false;
    use HasFactory;
    public function dictionary()
    {
        return $this->belongsTo(Dictionary::class);
    }
}
