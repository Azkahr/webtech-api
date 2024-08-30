<?php

namespace App\Http\Controllers\API;

use App\Models\Blog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BlogComment;
use App\Repository\UploadRepository;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    protected $upload;

    public function __construct()
    {
        $this->upload = new UploadRepository();
    }
    
    public function index() {
        return response()->json([
            'message' => 'All blog', 
            'data' => Blog::with(['comments.user', 'author'])->get()
        ]);
    }
    
    public function show($id) {
        $blog = Blog::where('id', $id)->with(['comments.user', 'author'])->firstOrFail();
        
        return response()->json([
            'message' => 'Blog detail', 
            'data' => $blog
        ]);
    }
    
    public function store(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required',
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
    
            $blog = Blog::create($data);
    
            return response()->json([
                'message' => 'Blog created',
                'data' => $blog
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Blog $blog) {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required',
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

            $data['image'] = $blog->image;
            $data['author'] = auth()->user()->id;

            if($request->file('image')) {
                $data['image'] = $this->upload->update($blog->image, $request->file('image'));
            }
    
            $blog->update($data);
    
            return response()->json([
                'message' => 'Blog updated',
                'data' => $blog->fresh()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Blog $blog) {
        $this->upload->delete($blog->image);
        
        BlogComment::where('blog_id', $blog->id)->delete();
        
        $blog->delete();

        return response()->json([
            'message' => 'Blog deleted',
        ]);
    }
}
