<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StatsController extends Controller
{
    use ApiResponseTrait;

    public function getStats(){
        $usersCount = User::all()->count();
        $postsCount = Post::all()->count();

        $users = User::all();
        $i = 0;
        foreach ($users as $user){
           $user = count($user->posts);
           if ($user == 0){
               ++$i;
           }
        }

        //cached the results
        Cache::put('usersCount', $usersCount);
        Cache::put('postsCount', $postsCount);
        Cache::put('usersWithZeroPosts', $i);

        return $this->apiResponse(["usersCount"=>$usersCount, "postsCount"=>$postsCount, "usersWithZeroPosts"=>$i], 'ok', 200);
    }
}
