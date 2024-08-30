<?php

namespace App\Http\Controllers\API;

use App\Models\Banner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repository\UploadRepository;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
    protected $upload;

    public function __construct()
    {
        $this->upload = new UploadRepository();
    }
    
    public function index() {
        return response()->json([
            'message' => 'All banner', 
            'data' => Banner::latest()->get()
        ]);
    }
    
    public function show(Banner $banner) {
        return response()->json([
            'message' => 'Banner detail', 
            'data' => $banner
        ]);
    }
    
    public function store(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required',
                'status' => 'required',
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

    public function update(Request $request, Banner $banner) {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required',
                'status' => 'required',
                'image' => 'required|max:1024|mimes:png,jpg,jpeg',
            ]);
    
            if($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid Data',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            $data = $request->all();
            unset($data['_method']);

            $data['image'] = $banner->image;

            if($request->file('image')) {
                $data['image'] = $this->upload->update($banner->image, $request->file('image'));
            }
    
            $banner->update($data);
    
            return response()->json([
                'message' => 'Banner updated',
                'data' => $banner->fresh()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Banner $banner) {
        $this->upload->delete($banner->image);
        
        $banner->delete();

        return response()->json([
            'message' => 'Banner deleted',
        ]);
    }
}
