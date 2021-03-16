<?php

namespace App\Http\QueryFilters\PassengerWagon;

use App\Http\QueryFilters\Filter;

class Sort extends Filter
{
    protected function applyFilter($builder)
    {
        return $builder->orderBy('number', request($this->filterName()));
    }
}
