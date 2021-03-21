<?php

namespace App\Http\QueryFilters\TractiveUnit;

use App\Http\QueryFilters\Filter;

class OwnerId extends Filter
{
    protected function applyFilter($builder)
    {
        return $builder->where('owner_id', '=', request($this->filterName()));
    }
}
