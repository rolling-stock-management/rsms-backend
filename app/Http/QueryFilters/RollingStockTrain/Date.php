<?php

namespace App\Http\QueryFilters\RollingStockTrain;

use App\Http\QueryFilters\Filter;

class Date extends Filter
{
    protected function applyFilter($builder)
    {
        return $builder->whereDate('date', '=', request($this->filterName()));
    }
}
