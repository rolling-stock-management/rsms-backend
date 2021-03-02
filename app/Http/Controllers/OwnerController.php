<?php

namespace App\Http\Controllers;

use App\Http\Resources\OwnerResource;
use App\Models\Owner;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OwnerController extends Controller
{
    /**
     * OwnerController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Owner::class, 'owner');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->has('no-pagination') && request('no-pagination')) {
            $owners = Owner::all();
        }
        else
        {
            $owners = Owner::paginate(10);
        }
        return OwnerResource::collection($owners);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function store(Request $request)
    {
        $owner = Owner::create($this->validateRequestData());

        return (new OwnerResource($owner))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Owner  $owner
     * @return OwnerResource|\Illuminate\Http\Response
     */
    public function show(Owner $owner)
    {
        return new OwnerResource($owner);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Owner  $owner
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function update(Request $request, Owner $owner)
    {
        $owner->update($this->validateRequestData());

        return (new OwnerResource($owner))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Owner  $owner
     * @return \Illuminate\Http\Response
     */
    public function destroy(Owner $owner)
    {
        $owner->delete();
        return response([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Validate data from request.
     *
     * @return mixed
     */
    private function validateRequestData()
    {
        return request()->validate([
            'name' => ['required', 'string'],
            'note' => ['sometimes', 'string']
        ]);
    }
}
