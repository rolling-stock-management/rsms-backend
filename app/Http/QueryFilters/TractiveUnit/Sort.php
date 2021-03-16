<?php

namespace App\Http\QueryFilters\TractiveUnit;

use App\Http\QueryFilters\Filter;

class Sort extends Filter
{
    protected function applyFilter($builder)
    {
        return $builder->orderBy('number', request($this->filterName()));
    }
}
