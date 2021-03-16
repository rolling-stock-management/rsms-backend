<?php

namespace App\Http\QueryFilters\FreightWagons;

use App\Http\QueryFilters\Filter;

class RepairValidUntilThisMonth extends Filter
{
    protected function applyFilter($builder)
    {
        if(!request($this->filterName()))
        {
            return $builder;
        }
        return $builder->whereYear('repair_valid_until', date("Y"))->whereMonth('repair_valid_until', date("m"));
    }
}
