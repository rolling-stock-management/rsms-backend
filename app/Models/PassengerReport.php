<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PassengerReport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['email', 'date', 'problem_description', 'wagon_number', 'train_id', 'wagon_id', 'image_file_name'];

    /**
     * The attributes that should be treated as dates.
     *
     * @var array
     */
    protected $dates = ['date'];

    /**
     * Set the date as an instance of Carbon
     *
     * @param $value
     * @return void
     */
    public function setDateAttribute($value)
    {
        if ($value == null || $value == '') {
            $this->attributes['date'] = null;
        } else {
            $this->attributes['date'] = Carbon::parse($value);
        }
    }

    /**
     * Get the train of the passenger report.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function train()
    {
        return $this->belongsTo(Train::class);
    }

    /**
     * Get the wagon of the passenger report.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function wagon()
    {
        return $this->belongsTo(PassengerWagon::class);
    }
}
