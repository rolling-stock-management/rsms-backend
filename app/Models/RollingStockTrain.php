<?php

namespace App\Models;

use App\Http\QueryFilters\RollingStockTrain as Filters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pipeline\Pipeline;

class RollingStockTrain extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['position', 'date', 'train_id', 'comment', 'user_id', 'trainable_id', 'trainable_type'];

    /**
     * The attributes that should be treated as dates.
     *
     * @var array
     */
    protected $dates = ['date'];

    /**
     * Get all records through filtering pipelines and paginate the result.
     *
     * @return mixed
     */
    public static function allRollingStockTrains()
    {
        return app(Pipeline::class)
            ->send(RollingStockTrain::query())
            ->through([
                Filters\TrainableType::class,
                Filters\Date::class,
                Filters\TrainId::class,
            ])
            ->thenReturn()
            ->paginate(10);
    }

    /**
     * Get the train which the rolling stock train belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function train()
    {
        return $this->belongsTo(Train::class);
    }

    /**
     * Get the user which the rolling stock train belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent trainable type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function trainable()
    {
        return $this->morphTo();
    }
}
