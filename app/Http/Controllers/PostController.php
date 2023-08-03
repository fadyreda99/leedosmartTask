<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\Tag;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    use ApiResponseTrait;

    public function index(Post $post)
    {
        $posts = Post::orderBy('pinned', 'desc')->with('tags')->get();
        $postsResou = PostResource::collection($posts);
        return $this->apiResponse($postsResou, 'ok', 200);

    }

    public function store(Request $request, Post $post)
    {
        if(!$request->hasFile('cover_image')) {
            return $this->apiResponse('upload file not found', 'not found', 404);
        }

        $validator = Validator::make($request->all(), [
           'title'=>'required',
           'body'=>'required',
           'cover_image'=>'required|image:jpeg,png,jpg,gif,svg|max:2048',
            'tags'=>'required',
            'pinned'=>'required|boolean'
        ]);

        if ($validator->fails()){
            return $this->apiResponse(null, $validator->errors(), 400);
        }

        $tagsId = explode(',',$request->tags );
        foreach ($tagsId as $tag){
            $tagId = Tag::where('id', $tag)->get()->count();
            if($tagId < 1){
                return $this->apiResponse(null, 'tags not found choose right tags', 400);
            }
        }

        $imageName = $request->cover_image->hashName();
        $moving = $request->cover_image->move(public_path('postsImage/' ), $imageName);

        if($moving){
            $StorePost = Post::create([
                'title'=>$request->title,
                'body'=>$request->body,
                'cover_image'=>$imageName,
                'user_id'=>auth()->user()->id,
                'pinned'=>$request->pinned,
            ]);

            $latestPost = Post::latest()->first();
            $latestPost->tags()->sync(explode(',',$request->tags ));

            if($StorePost ){
                $postRes = new PostResource($StorePost);
                return $this->apiResponse($postRes, 'post created', 200);
            }else{
                return $this->apiResponse(null, 'post not created', 400);
            }
        }
    }

    public function show($id)
    {
        $post = Post::find($id);
        if($post){
            $postRes = new PostResource($post);
            return $this->apiResponse($postRes, 'ok', 200);
        }else{
            return $this->apiResponse(null, 'post not found', 404);
        }
    }

    public function update(Request $request,$id)
    {
        $post = Post::find($id);

        if($post) {
            if ($request->hasFile('cover_image')) {
                $validator = Validator::make($request->all(), [
                    'title' => 'required',
                    'body' => 'required',
                    'cover_image' => 'required|image:jpeg,png,jpg,gif,svg|max:2048',
                    'pinned' => 'required|boolean'
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse(null, $validator->errors(), 400);
                }

                $imageName = $request->cover_image->hashName();
                $moving = $request->cover_image->move(public_path('postsImage/'), $imageName);

                if ($moving) {
                    $updatedPost = $post->update([
                        'title' => $request->title,
                        'body' => $request->body,
                        'cover_image' => $imageName,
                        'pinned' => $request->pinned,
                    ]);

                    if ($updatedPost) {
                        $postRes = new PostResource($post);
                        return $this->apiResponse($postRes, 'post updated', 201);
                    } else {
                        return $this->apiResponse(null, 'post not updated', 400);
                    }
                }
            } else {
                $validator = Validator::make($request->all(), [
                    'title' => 'required',
                    'body' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse(null, $validator->errors(), 400);
                }

                $updatedPost = $post->update($request->all());
                if ($updatedPost) {
                    $postRes = new PostResource($post);
                    return $this->apiResponse($postRes, 'post updated', 201);
                } else {
                    return $this->apiResponse(null, 'post not updated', 400);
                }
             }
        }else{
            return $this->apiResponse(null, 'post not found', 404);
        }
    }

    //soft deletes
    public function destroy($id)
    {
        $post = Post::find($id);
        if($post) {
            $deletedPost = $post->Delete();
            if($deletedPost){
                return $this->apiResponse(null, 'post deleted', 200);
            }
        }else{
            return $this->apiResponse(null, 'post not found', 404);
        }
    }

    public function deletedPosts(){
        $deletedPosts = Post::onlyTrashed()->get();
        $deletedPostsRes = PostResource::collection($deletedPosts);
        return $this->apiResponse($deletedPosts, 'ok', 200);
    }

    public function restoreFromDeletes($id){
        $deletedPost = Post::withTrashed()->where('id', $id);

        if($deletedPost){
            $restorePost  =$deletedPost->restore();

            if($restorePost){
                $postRes = new PostResource($restorePost);
                return $this->apiResponse(null, 'post restored', 201);
            }else{
                return $this->apiResponse(null, 'something wrong', 500);
            }
        }else{
            return $this->apiResponse(null, 'post not found', 404);
        }
    }
}
