<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PassengerWagon extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number',
        'letter_marking',
        'tare',
        'total_weight',
        'seats_count',
        'max_speed',
        'length',
        'brake_marking',
        'owner_id',
        'status_id',
        'repair_date',
        'repair_valid_until',
        'repair_workshop_id',
        'depot_id',
        'other_info'
    ];

    /**
     * The attributes that should be treated as dates.
     *
     * @var array
     */
    protected $dates = ['repair_date', 'repair_valid_until'];

    /**
     * Set the repair date as an instance of Carbon
     *
     * @param $value
     * @return void
     */
    public function setRepairDateAttribute($value)
    {
        if ($value == null || $value == '') {
            $this->attributes['repair_date'] = null;
        } else {
            $this->attributes['repair_date'] = Carbon::parse($value);
        }
    }

    /**
     * Set the repair valid until date as an instance of Carbon
     *
     * @param $value
     * @return void
     */
    public function setRepairValidUntilAttribute($value)
    {
        if ($value == null || $value == '') {
            $this->attributes['repair_valid_until'] = null;
        } else {
            $this->attributes['repair_valid_until'] = $value;
        }
    }

    /**
     * Get stylized number of the model with spaces and dashes.
     *
     * @return string
     */
    public function getStylizedNumber()
    {
        $ab = substr($this->number, 0, 2);
        $cd = substr($this->number, 2, 2);
        $ef = substr($this->number, 4, 2);
        $gh = substr($this->number, 6, 2);
        $xyz = substr($this->number, 8, 3);
        $k = substr($this->number, 11);
        //AB CD EF-GH XYZ-K
        $number = $ab . ' ' . $cd . ' ' . $ef . '-' . $gh . ' ' . $xyz . '-' . $k;
        return $number;
    }

    /**
     * Get short stylized number of the model with spaces and dashes.
     *
     * @return string
     */
    public function getShortStylizedNumber()
    {
        $ef = substr($this->number, 4, 2);
        $gh = substr($this->number, 6, 2);
        $xyz = substr($this->number, 8, 3);
        $k = substr($this->number, 11);
        $number = $ef . '-' . $gh . ' ' . $xyz . '-' . $k;
        return $number;
    }

    /**
     * Get the wagon type of the passenger wagon.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(PassengerWagonType::class);
    }

    /**
     * Get the depot of the passenger wagon.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function depot()
    {
        return $this->belongsTo(Depot::class);
    }

    /**
     * Get the owner of the passenger wagon.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    /**
     * Get the status of the passenger wagon.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Get teh repair workshop of the passenger wagon.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function repairWorkshop()
    {
        return $this->belongsTo(RepairWorkshop::class);
    }
}
