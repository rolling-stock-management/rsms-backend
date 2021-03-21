<?php

namespace App\Http\QueryFilters\FreightWagons;

use App\Http\QueryFilters\Filter;

class OwnerId extends Filter
{
    protected function applyFilter($builder)
    {
        return $builder->where('owner_id', '=', request($this->filterName()));
    }
}
