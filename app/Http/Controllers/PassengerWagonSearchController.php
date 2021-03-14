<?php

namespace App\Http\Controllers;

use App\Http\Resources\PassengerWagonResource;
use App\Models\PassengerWagon;
use Illuminate\Http\Request;

class PassengerWagonSearchController extends Controller
{
    /**
     * Search Passenger wagons and return top 5 results.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('viewAny', PassengerWagon::class);

        $data = request()->validate([
            'search_term' => ['required', 'string']
        ]);

        $wagons = PassengerWagon::search($data['search_term'])->get()->take(5);

        return PassengerWagonResource::collection($wagons);
    }
}
