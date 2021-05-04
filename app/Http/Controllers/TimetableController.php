<?php

namespace App\Http\Controllers;

use App\Http\Resources\TimetableResource;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TimetableController extends Controller
{
    /**
     * TimetableController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Timetable::class, 'timetable');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->has('no-pagination') && request('no-pagination')) {
            $timetables = Timetable::all();
        }
        else
        {
            $timetables = Timetable::paginate(10);
        }
        return TimetableResource::collection($timetables);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function store(Request $request)
    {
        $timetable = Timetable::create($this->validateRequestData());

        return (new TimetableResource($timetable))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Timetable  $timetable
     * @return TimetableResource|\Illuminate\Http\Response
     */
    public function show(Timetable $timetable)
    {
        return new TimetableResource($timetable);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Timetable  $timetable
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function update(Request $request, Timetable $timetable)
    {
        $timetable->update($this->validateRequestData());

        return (new TimetableResource($timetable))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Timetable  $timetable
     * @return \Illuminate\Http\Response
     */
    public function destroy(Timetable $timetable)
    {
        $timetable->delete();
        return response([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Validate data from the request.
     *
     * @return mixed
     */
    private function validateRequestData()
    {
        return request()->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
        ]);
    }
}
