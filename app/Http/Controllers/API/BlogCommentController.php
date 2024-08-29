<?php

namespace App\Http\Controllers\API;

use App\Models\Blog;
use App\Models\BlogComment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BlogCommentController extends Controller
{
    public function index(Request $request) {
        $validator = Validator::make($request->all(), [
            'blog_id' => 'required'
        ]);
        
        if($validator->fails()) {
            return response()->json([
                'message' => 'Invalid',
                'error' => $validator->errors()
            ], 422);
        }
        
        $data = BlogComment::where('blog_id', $request->blog_id)->get();
        
        return response()->json([
            'message' => 'Get blog comment', 
            'data' => $data
        ]);
    }
    
    public function show(BlogComment $blogComment) {
        return response()->json([
            'message' => 'Blog comment detail', 
            'data' => $blogComment
        ]);
    }
    
    public function store(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'website' => 'required',
                'subject' => 'required',
                'blog_id' => 'required',
            ]);
    
            if($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid Data',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            $blogExists = Blog::where('id', $request->blog_id)->first();

            if(!$blogExists) return response()->json(['message' => 'Blog not found'], 404);
            
            $data = $request->all();
    
            $data['user_id'] = auth()->user()->id;
    
            $blogComment = BlogComment::create($data);
    
            return response()->json([
                'message' => 'Blog comment created',
                'data' => $blogComment
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, BlogComment $blogComment) {
        try {
            $validator = Validator::make($request->all(), [
                'website' => 'required',
                'subject' => 'required',
                'blog_id' => 'required',
            ]);
    
            if($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid Data',
                    'errors' => $validator->errors()
                ], 422);
            }

            $blogExists = Blog::where('id', $request->blog_id)->first();

            if(!$blogExists) return response()->json(['message' => 'Blog not found'], 404);
    
            $data = $request->all();
            unset($data['_method']);

            $data['user_id'] = auth()->user()->id;

            $blogComment->update($data);
    
            return response()->json([
                'message' => 'BlogComment updated',
                'data' => $blogComment->fresh()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(BlogComment $blogComment) {
        $blogComment->delete();

        return response()->json([
            'message' => 'Blog comment deleted',
        ]);
    }
}
