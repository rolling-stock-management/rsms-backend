<?php

namespace App\Http\QueryFilters\Repair;

use App\Http\QueryFilters\Filter;

class StartDateAfter extends Filter
{
    protected function applyFilter($builder)
    {
        return $builder->whereDate('start_date', '>=', request($this->filterName()));
    }
}
