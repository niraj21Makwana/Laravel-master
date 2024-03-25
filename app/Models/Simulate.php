<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Simulate extends Model
{
    use HasFactory;

    protected $table = 'simulates';

    protected $fillable = ['percentage_of_winners'];
    
    public function prize(){
        return $this->belongsTo(Prize::class);
    }
}
