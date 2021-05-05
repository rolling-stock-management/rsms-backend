<?php

namespace App\Http\Controllers;

use App\Http\Resources\TrainResource;
use App\Models\Train;

class TrainSearchController extends Controller
{
    /**
     * Search Trains and return top 5 results.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {

        $data = request()->validate([
            'search_term' => ['required', 'string']
        ]);

        $trains = Train::search($data['search_term'])->get()->take(5);

        return TrainResource::collection($trains);
    }
}
