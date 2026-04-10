<?php

namespace Modules\Hotel\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AmenityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('hotel::index');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('hotel::show');
    }

  
 
}
