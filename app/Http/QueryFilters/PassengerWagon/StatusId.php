<?php

namespace App\Http\QueryFilters\PassengerWagon;

use App\Http\QueryFilters\Filter;

class StatusId extends Filter
{
    protected function applyFilter($builder)
    {
        return $builder->where('status_id', '=', request($this->filterName()));
    }
}
