<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function index(){
        return Inertia::render('Resources/Index');
    }
}
