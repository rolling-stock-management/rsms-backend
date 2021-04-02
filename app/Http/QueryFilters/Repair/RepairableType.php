<?php

namespace App\Http\QueryFilters\Repair;

use App\Http\QueryFilters\Filter;

class RepairableType extends Filter
{
    protected function applyFilter($builder)
    {
        //Array of available repairable models.
        $morphs = [
            'App\Models\PassengerWagon',
            'App\Models\FreightWagon',
            'App\Models\TractiveUnit',
        ];

        //Get the id from the request and transform it to an array index.
        $id = request($this->filterName()) - 1;

        //If index is outside of array don't apply the filter.
        if($id > count($morphs))
        {
            return $builder;
        }

        return $builder->whereHasMorph('repairable', $morphs[$id]);
    }
}
