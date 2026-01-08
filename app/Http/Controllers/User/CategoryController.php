<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(string $slug) {
      
      return view('user.categories.index', ['data' => $slug]);
    }
}
