<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Role::class, 'role');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();
        return RoleResource::collection($roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function store(Request $request)
    {
        $data = $this->validateRequest();
        $role = Role::create($data);

        if (array_key_exists('permission_ids',$data)) {
            $role->permissions()->sync($data['permission_ids']);
        }
        return (new RoleResource($role))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return RoleResource|\Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        return new RoleResource($role);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object
     */
    public function update(Request $request, Role $role)
    {
        $data = $this->validateRequest();
        $role->update($data);
        if (array_key_exists('permission_ids',$data)) {
            $role->permissions()->sync($data['permission_ids']);
        }
        return (new RoleResource($role))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return response([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Validate data from request.
     *
     * @return mixed
     */
    private function validateRequest()
    {
        return request()->validate([
            'name' => ['required', 'string', 'unique:App\Models\Role,name'],
            'permission_ids' => ['sometimes', 'array', 'nullable']
        ]);
    }
}
