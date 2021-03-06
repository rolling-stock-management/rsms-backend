<?php

namespace App\Http\QueryFilters\Repair;

use App\Http\QueryFilters\Filter;

class EndDateAfter extends Filter
{
    protected function applyFilter($builder)
    {
        return $builder->whereDate('end_date', '>=', request($this->filterName()));
    }
}
