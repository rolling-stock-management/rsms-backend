<?php

namespace App\Http\QueryFilters\Repair;

use App\Http\QueryFilters\Filter;

class WorkshopId extends Filter
{
    protected function applyFilter($builder)
    {
        return $builder->where('workshop_id', '=', request($this->filterName()));
    }
}
