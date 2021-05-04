<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['start_date', 'end_date'];

    /**
     * The attributes that should be treated as dates.
     *
     * @var array
     */
    protected $dates = ['start_date', 'end_date'];

    /**
     * Set the start date as an instance of Carbon
     *
     * @param $value
     * @return void
     */
    public function setStartDateAttribute($value)
    {
        if ($value == null || $value == '') {
            $this->attributes['start_date'] = null;
        } else {
            $this->attributes['start_date'] = Carbon::parse($value);
        }
    }

    /**
     * Set the end date as an instance of Carbon
     *
     * @param $value
     * @return void
     */
    public function setEndDateAttribute($value)
    {
        if ($value == null || $value == '') {
            $this->attributes['end_date'] = null;
        } else {
            $this->attributes['end_date'] = Carbon::parse($value);
        }
    }
}
