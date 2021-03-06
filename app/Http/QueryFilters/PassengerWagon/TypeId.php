<?php


namespace App\Http\QueryFilters\PassengerWagon;

use App\Http\QueryFilters\Filter;

class TypeId extends Filter
{
    protected function applyFilter($builder)
    {
        return $builder->where('type_id', '=', request($this->filterName()));
    }
}
