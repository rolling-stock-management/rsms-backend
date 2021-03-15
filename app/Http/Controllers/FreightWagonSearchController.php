<?php

namespace App\Http\Controllers;

use App\Http\Resources\FreightWagonResource;
use App\Models\FreightWagon;

class FreightWagonSearchController extends Controller
{
    /**
     * Search Freight wagons and return top 5 results.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('viewAny', FreightWagon::class);

        $data = request()->validate([
            'search_term' => ['required', 'string']
        ]);

        $wagons = FreightWagon::search($data['search_term'])->get()->take(5);

        return FreightWagonResource::collection($wagons);
    }
}
