<?php

namespace App\Http\Controllers;

use App\Comment;
use \Illuminate\Http\Request;
use \Illuminate\Http\Response;
use Illuminate\Support\Str;
use \App\Event;
use App\JenisEvent;
use \App\Image;
use App\Like;
use App\Postingan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Routing\UrlGenerator;
use Laravel\Lumen\Routing\UrlGenerator;

class PostController extends Controller
{
    public function addPostingan(Request $request, UrlGenerator $url)
    {
        if ($request->filled(['user_id', 'caption', 'event_id'])) {
            $gambar = array();
            $postingan = Postingan::create([
                'user_id' => $request->input('user_id'),
                'caption' => $request->input('caption'),
                'event_id' => $request->input('event_id')
            ]);

            if ($request->hasFile('photo')) {
                $files = $request->file('photo');
                foreach ($files as $file) {
                    $filename = Str::random(30) . '.jpg';
                    $file->move(base_path('public/images/'), $filename);
                    $photourl = $url->to('/images') . '/' . $filename;
                    Image::create([
                        'post_id' => $postingan->id,
                        'photo_url' => 'http://192.168.42.93/mesjidkuapi/public/images/' . $filename
                    ]);

                    $gambar[] = $photourl;
                }
            }
            $data['postingan'] = $postingan;
            $data['photo_url'] = $gambar;

            return response()->json([
                'status' => true,
                'messages' => 'Postingan Berhasil Ditambahkan!!!',
                'data' => null
            ]);
        }
    }


    public function getAllPostingan(Request $request)
    {


        $data = null;
        $gambar = array();
        $postingans = array();
        $i = 0;
        $j = 0;
    
        $like = Postingan::orderBy('postingan.created_at', 'desc')->join('like', 'postingan.id', '=', 'post_id')->get();
        // $postingan = Postingan::orderBy('created_at', 'asc')->get();
        $postingan = Postingan::orderBy('postingan.created_at', 'desc')->join('users', 'postingan.user_id', '=', 'users.id')->join('event', 'event_id', '=', 'event.id')->join('jenis_event', 'jenis_event_id', '=' ,'jenis_event.id')->select('postingan.id', 'users.name', 'users.photo', 'caption', 'event_name', 'jenis_event.jenis_event', 'postingan.updated_at')->get();
    
        foreach ($postingan as $onepost) {
            $images = Image::where('post_id', '=', $onepost->id)->select('photo_url')->get();
            foreach ($images as $image) {
                $gambar[] = $image['photo_url'];
                $i++;
            }
        //    if($j == 1){
        //     return $gambar;
        //    }
            $postingans[] = $onepost;
            
    
            
            $data[$j]['comment'] = count(Comment::where('post_id', '=', $onepost->id)->get());
            $data[$j]['like'] = count(Like::where('post_id', '=', $onepost->id)->get());
            $data[$j]['post'] = $onepost;
            $data[$j]['url'] = $gambar;
            unset($gambar);
            $gambar = array();
            $j++;
        }

        // return $gambar;

        return response()->json([
            'status' => true,
            'messages' => 'pesan',
            'data' => $data
        ]);
    }

    public function getPostinganById($id)
    {
        $data = null;
        $gambar = array();
        $postingans = array();
        $i = 0;
        $j = 0;
        $postingan = Postingan::where('id', $id)->get();
        foreach ($postingan as $onepost) {
            $images = Image::where('post_id', '=', $onepost->id)->select('photo_url')->get();
            foreach ($images as $image) {
                $gambar[$i] = $image['photo_url'];
                $i++;
            }
            $postingans[] = $onepost;


            $data[$j]['post'] = $onepost;
            $data[$j]['url'] = $gambar;
            unset($gambar);
            $gambar = array();
            $j++;
        }

        return response()->json([
            'status' => true,
            'messages' => 'pesan',
            'data' => $data
        ]);
    }

    public function getPost(Request $request){
        $postingan = Event::whereDate('created_at', Carbon::yesterday())->orWhereDate('created_at', Carbon::today())->get();
        // return $postingan;
        $data = array();
        $count = 0;
        foreach($postingan as $post){
            $data[$count]['id'] = $post->id;
            $data[$count]['jenis_event'] = $post->event_name;
            // return $po
            $count++;
        }
        return $data;
    }
}
