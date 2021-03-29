<?php

namespace App\Http\Controllers;

use \Illuminate\Http\Request;
use \Illuminate\Http\Response;
use Illuminate\Support\Str;
use \App\Event;
use App\Postingan;
use \App\User;
use App\Ustad;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Routing\UrlGenerator;
use Laravel\Lumen\Routing\UrlGenerator;

class UserController extends Controller
{
    public function userUpdate( Request $request, UrlGenerator $url, $id)
    {
        $user = User::where('id', '=', $id)->first();
        $photourl = null;
        if ($request->filled([
            'email','name','no_hp','gender','role', 'status','password' ])){
                if($request->hasFile('photo')){
                    $path = $user->photo;
                    $lastpath = explode('/', $path);
                    $filename = Str::random(30) . '.jpg';
                    $file = $request->file('photo');
                    $file->move(base_path('public/images/'), $filename);
                    unlink(base_path('public/images/' .  $lastpath[sizeof($lastpath)-1]));
                    $photourl = $url->to('/images') . '/' . $filename;
                }
                if($photourl != null){
                    $user->update([
                        'email' => $request->input('email'),
                        'name' => $request->input('name'),
                        'no_hp' => $request->input('no_hp'),
                        'gender' => $request->input('gender'),
                        'role' => $request->input('role'),
                        'status' => $request->input('status'),
                        'photo' => $photourl,
                        'password' => app('hash')->make($request->password)
                    ]);

                    return response()->json([
                    'status'=> true,
                    'messages'=> 'Update Berhasil!!',
                    'data'=> $user
                    ]);
                }else{
                    $user->update([
                        'email' => $request->input('email'),
                        'name' => $request->input('name'),
                        'no_hp' => $request->input('no_hp'),
                        'gender' => $request->input('gender'),
                        'role' => $request->input('role'),
                        'status' => $request->input('status'),
                        'password' => app('hash')->make($request->password)
                    ]);

                     return response()->json([
                    'status'=> true,
                    'messages'=> 'Update Berhasil!!',
                    'data'=> $user
                    ]);
                }
        }else{
            return response()->json([
            'status'=> false,
            'messages'=> 'Field Wajib Diisi!!',
            'data'=> $user
            ]);
        }
           
    }

    public function getUserById(Request $request, $id){
        return User::where('id', '=', $id)->first();
    }
    
    public function getAllUser(){
        return User::orderBy('id', 'asc')->get();
    }

    public function getUstad(Request $request){
    return User::where('role', '=', '0')->get();
    }

    public function postingan($id){
    $postingan = Postingan::where('user_id', '=', $id)->get();
    $user = User::where('id', '=', $id)->get();

    $data['user'] = $user;
    $data['postingan'] = $postingan;
    return response()->json([
    'status'=> true,
    'messages'=> 'Berhasil',
    'data'=> $data
    ]);
    }

    public function searchUser(Request $request){
     
        if ($request->has('q')) {
            if($request->q == "cek"){
                $cari = $request->q;
           
                $data =User::select('id', 'name', 'photo', 'email')->get();
                return response()->json($data);
            }else{
                $cari = $request->q;
           
            $data =User::select('id', 'name', 'photo', 'email')->where('email', 'LIKE',  "%{$cari}%")
            ->orWhere('name', 'LIKE', "%{$cari}%") ->get();
            return response()->json($data);
            }
            
        }
    
    }

    public function addUstad(Request $request){
        $ustadList = $request->input('ustad');
        $user_id = $request->input('user_id');
        $role = $request->input('role');
        $event = $request->input('event');
$count = 0;
        foreach($ustadList as $ustad){
            Ustad::create([
            'user_id'=> $user_id[$count],
            'nama_ustad'=> $ustad,
            'role'=> $role[$count],
            'ustad_event_id'=>$event[$count]
        ]);
        
            $count++;
        }
        

        return response()->json([
        'status'=> true,
        'messages'=> 'pesan'
        ]);
    }


    public function getUserData(Request $request){
      
       if($request->hasFilled(['user_id', 'password'])){
        $userInput = $request->input('user_id');
        $password = $request->input('password');

        $userDb = User::create([
            'nama' => $userInput,
            'password'=> $password
        ]);

        $userList = array();
        foreach($userList as $user){
            $data = $user->data;
        }

        return $userDb;
       } else{
           return 'data didnt exist';
       }

       return 'get another data'; 
    }
}
