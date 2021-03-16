<?php

namespace App\Http\QueryFilters\TractiveUnit;

use App\Http\QueryFilters\Filter;

class DepotId extends Filter
{
    protected function applyFilter($builder)
    {
        return $builder->where('depot_id', '=', request($this->filterName()));
    }
}
