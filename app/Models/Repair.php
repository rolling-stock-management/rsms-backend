<?php

namespace App\Models;

use App\Http\QueryFilters\Repair as Filters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pipeline\Pipeline;

class Repair extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['short_description', 'type_id', 'workshop_id', 'start_date', 'end_date', 'description', 'repairable_id', 'repairable_type'];

    /**
     * The attributes that should be treated as dates.
     *
     * @var array
     */
    protected $dates = ['start_date', 'end_date'];

    /**
     * Get all records through filtering pipelines and paginate the result.
     *
     * @return mixed
     */
    public static function allRepairs()
    {
        return app(Pipeline::class)
            ->send(Repair::query())
            ->through([
                Filters\RepairableType::class,
                Filters\StartDateBefore::class,
                Filters\StartDateAfter::class,
                Filters\EndDateBefore::class,
                Filters\EndDateAfter::class,
                Filters\TypeId::class,
                Filters\WorkshopId::class,
            ])
            ->thenReturn()
            ->paginate(10);
    }

    /**
     * Get the type which the repair belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(RepairType::class);
    }

    /**
     * Get the workshop which the repair belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workshop()
    {
        return $this->belongsTo(RepairWorkshop::class);
    }

    /**
     * Get the parent repairable model.
     */
    public function repairable()
    {
        return $this->morphTo();
    }
}
