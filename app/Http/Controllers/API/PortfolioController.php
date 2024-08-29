<?php

namespace App\Http\Controllers\API;

use App\Models\Portfolio;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repository\UploadRepository;
use Illuminate\Support\Facades\Validator;

class PortfolioController extends Controller
{
    protected $upload;

    public function __construct()
    {
        $this->upload = new UploadRepository();
    }
    
    public function index() {
        \Log::info('hit');
        return response()->json([
            'message' => 'All banner', 
            'data' => Portfolio::all()
        ]);
    }
    
    public function show(Portfolio $portfolio) {
        return response()->json([
            'message' => 'Portfolio detail', 
            'data' => $portfolio
        ]);
    }
    
    public function store(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'category_id' => 'required',
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
            $data['author'] = auth()->user()->id;
    
            $portfolio = Portfolio::create($data);
    
            return response()->json([
                'message' => 'Portfolio created',
                'data' => $portfolio
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Portfolio $portfolio) {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'category_id' => 'required',
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

            $data['image'] = $portfolio->image;
            $data['author'] = auth()->user()->id;

            if($request->file('image')) {
                $data['image'] = $this->upload->update($portfolio->image, $request->file('image'));
            }
    
            $portfolio->update($data);
    
            return response()->json([
                'message' => 'Portfolio updated',
                'data' => $portfolio->fresh()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Portfolio $portfolio) {
        $this->upload->delete($portfolio->image);
        
        $portfolio->delete();

        return response()->json([
            'message' => 'Portfolio deleted',
        ]);
    }
}
