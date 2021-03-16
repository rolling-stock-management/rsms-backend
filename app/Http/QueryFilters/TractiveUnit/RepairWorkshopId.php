<?php

namespace App\Http\QueryFilters\TractiveUnit;

use App\Http\QueryFilters\Filter;

class RepairWorkshopId extends Filter
{
    protected function applyFilter($builder)
    {
        return $builder->where('repair_workshop_id', '=', request($this->filterName()));
    }
}
