<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'description', 'file_name', 'user_id', 'date'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['date'];

    /**
     * Set the image's date as an instance of carbon
     *
     * @param string $value
     * @return void
     */
    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::parse($value);
    }

    /**
     * Get all of the passenger wagons that are assigned this image.
     */
    public function passengerWagons()
    {
        return $this->morphedByMany(PassengerWagon::class, 'imageable');
    }
}
