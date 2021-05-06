<?php

namespace App\Http\QueryFilters\RollingStockTrain;

use App\Http\QueryFilters\Filter;

class TrainId extends Filter
{
    protected function applyFilter($builder)
    {
        return $builder->where('train_id', '=', request($this->filterName()));
    }
}
