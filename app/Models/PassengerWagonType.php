<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PassengerWagonType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'repair_valid_for', 'interior_type_id'];

    /**
     * Get the passenger wagon interior type that the type belongs to.
     */
    public function interiorType()
    {
        return $this->belongsTo(PassengerInteriorType::class);
    }
}
