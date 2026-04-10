<?php

namespace Modules\Hotel\Http\Controllers\Dashboard\V1\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('hotel::index');
    }

   
   
}
