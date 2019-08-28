<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AuthorCommentsResource;
use App\Http\Resources\AuthorPostsResourc;
use App\Http\Resources\TokenResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\UsersResource;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $users = User::paginate(env('AUTHORS_PER_PAGE'));
       return new UsersResource($users);
    }


    public function store(Request $request)
    {
            $request ->validate([
                'name' =>'required',
                'email' =>'required',
                'password' =>'required'
            ]);

            $user = new User();
            $user -> name = $request ->get('name');
            $user -> email = $request ->get('email');
            $user -> password = Hash::make($request ->get('password'));
            $user-> save();

            return new UserResource($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new UserResource(User::find( $id ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function posts($id){
        $user = User::find( $id );
        $posts = $user->posts()->paginate(env('POSTS_PER_PAGE'));
        return new AuthorPostsResourc($posts);
    }

    public function comments($id){
        $user = User::find( $id );
        $comments = $user->comments()->paginate(env('COMMENTS_PER_PAGE'));
        return new AuthorCommentsResource($comments);
    }

    public function getToken (Request $request){
        $request -> validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $credentials = $request ->only('email', 'password');
        if(Auth::attempt($credentials)){
            $user = User::where('email', $request -> get('email')) -> first();
            return new TokenResource(['token' => $user -> api_token]);
        }
        return 'not found';
    }


}
