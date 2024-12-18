<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubCategoryController extends Controller
{
    public function __construct(protected SubCategory $model) {}

    public function index() {

        return Inertia::render('Resources/SubCategories/Index');

    }

}
