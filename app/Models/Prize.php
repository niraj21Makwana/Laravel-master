<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Prize extends Model
{

    protected $guarded = ['id'];

    public  static function nextPrize()
    {   
        
    }

    public static function validateProbability($newProbability)
    {
        // Calculate the total probability of existing prizes
        $totalProbability = self::sum('probability');

        // Calculate the total probability after adding the new prize
        $updatedTotalProbability = $totalProbability + $newProbability;

        // Check if the updated total probability exceeds 100%
        if ($updatedTotalProbability <= 100) {
            return true; // Total probability is within acceptable range
        } else {
            return false; // Total probability exceeds 100%
        }
    }

    public function simulationResults(){
        return $this->hasMany(Simulate::class);
    }
}
