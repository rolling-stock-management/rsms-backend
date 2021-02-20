<?php

namespace App\Http\Controllers;

use App\Http\Resources\PermissionResource;
use App\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Permission::class, 'permission');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::all();

        return PermissionResource::collection($permissions);
    }
}
