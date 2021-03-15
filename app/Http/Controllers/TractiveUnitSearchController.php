<?php

namespace App\Http\Controllers;

use App\Http\Resources\TractiveUnitResource;
use App\Models\TractiveUnit;

class TractiveUnitSearchController extends Controller
{
    /**
     * Search Tractive units and return top 5 results.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('viewAny', TractiveUnit::class);

        $data = request()->validate([
            'search_term' => ['required', 'string']
        ]);

        $wagons = TractiveUnit::search($data['search_term'])->get()->take(5);

        return TractiveUnitResource::collection($wagons);
    }
}
