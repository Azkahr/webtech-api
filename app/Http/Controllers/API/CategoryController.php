<?php

namespace App\Http\Controllers\API;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index() {
        return response()->json([
            'message' => 'All category', 
            'data' => Category::all()
        ]);
    }
    
    public function show(Category $category) {
        return response()->json([
            'message' => 'Category detail', 
            'data' => $category
        ]);
    }
    
    public function store(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);
    
            if($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid Data',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            $data = $request->all();
    
            $category = Category::create($data);
    
            return response()->json([
                'message' => 'Category created',
                'data' => $category
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Category $category) {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);
    
            if($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid Data',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            $data = $request->all();
            unset($data['_method']);
    
            $category->update($data);
    
            return response()->json([
                'message' => 'Category updated',
                'data' => $category->fresh()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Category $category) {
        $category->delete();

        return response()->json([
            'message' => 'Category deleted',
        ]);
    }
}
