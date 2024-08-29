<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index() {

    }
    
    public function store(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required',
                'image' => 'required|max:1024|mimes:png,jpg,jpeg',
            ]);
    
            if($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid Data',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            $data = $request->all();
    
            $data['image'] = $this->upload->save($request->file('image'));
            $data['status'] = $request->active;
    
            $banner = Banner::create($data);
    
            return response()->json([
                'message' => 'Banner created',
                'data' => $banner
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request) {
        
    }

    public function destroy(Request $request) {

    }
}
