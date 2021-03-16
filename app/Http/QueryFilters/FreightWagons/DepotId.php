<?php

namespace App\Http\QueryFilters\FreightWagons;

use App\Http\QueryFilters\Filter;

class DepotId extends Filter
{
    protected function applyFilter($builder)
    {
        return $builder->where('depot_id', '=', request($this->filterName()));
    }
}
