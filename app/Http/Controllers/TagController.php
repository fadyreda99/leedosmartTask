<?php

namespace App\Http\Controllers;

use App\Http\Resources\TagResource;
use App\Models\Tag;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
      $tags = TagResource::collection(Tag::get());
      return $this->apiResponse($tags, 'ok', 200);
    }

    public function store(Request $request)
    {
       $validator = Validator::make($request->all(), [
           'name'=>'required|unique:tags',
       ]);

        if($validator->fails()){
            return $this->apiResponse(null, $validator->errors(), 400);
        }

       $tag = Tag::create($request->all());
       if($tag){
           $tagRes = new TagResource($tag);
           return $this->apiResponse($tagRes, 'tag created', 201);
       }else{
           return $this->apiResponse(null, 'tag not created', 400);
       }
    }

    public function update(Request $request, $id)
    {
        $tag = Tag::find($id);

        if($tag){
            $validator = Validator::make($request->all(), [
               'name' => 'required|unique:tags',
            ]);

            if($validator->fails()){
                return $this->apiResponse(null, $validator->errors(), 400);
             }

            $updatedTag = $tag->update($request->all());
            if($updatedTag){
                $tagRes = new TagResource($tag);
                return $this->apiResponse($tagRes, 'tag updated', 201);
            }else{
                return $this->apiResponse(null, 'tag not updated', 400);
            }
        }else{
            return $this->apiResponse(null, 'tag not found', 404);
        }
    }

    public function destroy($id)
    {
        $tag = Tag::find($id);
        if($tag){
            $deletedTag = Tag::destroy($id);
            if($deletedTag){
                return $this->apiResponse(null, 'tag deleted', 200);
            }else{
                return $this->apiResponse(null, 'something wrong try again later',500 );
            }
        }else{
            return $this->apiResponse(null, 'tag not found', 404);
        }

    }
}
