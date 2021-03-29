<?php

namespace App\Http\Controllers;

use \Illuminate\Http\Request;
use \Illuminate\Http\Response;
use Illuminate\Support\Str;
use \App\Event;
use App\JenisEvent;
use \App\Mesjid;
use \App\User;
use App\Ustad;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Routing\UrlGenerator;
use Laravel\Lumen\Routing\UrlGenerator;

class EventController extends Controller
{
    public function addEvent(Request $request, UrlGenerator $url)
    {
        // return  $request;
        if ($request->filled(['event_name', 'mesjid_id', 'deskripsi', 'jenis_event', 'jadwal'])) {
            if ($request->hasFile('photo_event')) {
                $file = $request->file('photo_event');
                $filename = Str::random(30) . '.jpg';
                $file->move(\base_path('public/images/'), $filename);
                $filespath = $url->to('/images') . '/' . $filename;
                $event = Event::create([
                    'event_name' => $request->input('event_name'),
                    'deskripsi' => $request->input('deskripsi'),
                    'jadwal' => $request->input('jadwal'),
                    'photo_event' => $filespath,
                    'jenis_event_id' => $request->jenis_event,
                    'id_mesjid' => $request->input('mesjid_id')

                ]);
                return response()->json([
                    'status' => true,
                    'messages' => 'Event berhasil ditambahkan!!!',
                    'id' => $event->id
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'messages' => 'Event berhasil ditambahkan!!!',
                    'id' => 5
                ]);
            }
            return  'sdsd';
        }
        return  'sdsd';
    }

    public function addJenisEvent(Request $request)
    {
        if ($request->filled('jenis_event')) {
            $ustad = JenisEvent::create([
                'jenis_event' => $request->jenis_event
            ]);
            return response()->json([
                'status' => true,
                'messages' => 'berhasil menambahkan Jenis Event!!',
                'data' => $ustad
            ]);
        }
    }

    public function getAllJenisEvent()
    {
        return JenisEvent::orderBy('created_at', 'asc')->get();
    }


    public function event($id)
    {
        if ($id) {
            $event = Event::where('event.id', '=', $id)->join('location', 'event.location_id', '=', 'location.id')
                ->join('users', 'event.id_ustad', '=', 'users.id')
                ->select(
                    'event.id',
                    'event.photo_event',
                    'event.deskripsi',
                    'users.photo',
                    'location.alamat',
                    'location.lat',
                    'location.lng',
                    'event.jadwal'
                )->get();
            return $event;
        }
    }

    public function allEvent(Request $request)
    {
        $event = Event::orderBy('event.created_at', 'asc')
            ->join('jenis_event', 'event.jenis_event_id', '=', 'jenis_event.id')

            ->select(
                'event.id',
                'event.event_name',
                'users.name',
                'event.deskripsi',
                'jenis_event.jenis_event',
                'location.location_name',
                'location.alamat',
                'event.jadwal',
                'location.lat',
                'location.lng',
                'users.photo',
                'event.photo_event'
            )->get();
        return $event;
    }

    public function getRam()
{
    // {
    //     $free = shell_exec('free');
    //     $free = (string)trim($free);
    //     $free_arr = explode("\n", $free);
    //     $mem = explode(" ", $free_arr[1]);
    //     $mem = array_filter($mem);
    //     $mem = array_merge($mem);
    //     $memory_usage = $mem[2]/$mem[1]*100;
    
        return Shell_Exec('powershell -InputFormat none -ExecutionPolicy ByPass -NoProfile -Command "Get-Process"');
    
    }



    public function addMesjid(Request $request)
    { $output = array();
        exec('da.bat', $output); 
        return pclose(popen("start /B ". "da.bat", "r")); 
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = Str::random(30) . '.jpg';
            $file->move(\base_path('public/images/mesjid'), $filename);

            $mesjid = Mesjid::create([
                'nama_mesjid' =>  $request->input('nama'),
                'alamat_lengkap' => $request->input('alamat'),
                'lat' => $request->input('lat'),
                'lng' => $request->input('lng'),
                'photo_mesjid' => 'http://192.168.42.93/mesjidkuapi/public/images/mesjid' . $filename
            ]);

            return response()->json([
                'status' => true,
                'messages' => 'Berhasil Menambahkan Mesjid'
            ]);
        }
    }

    function searchmesjid(Request $request)
    {
        $cari = $request->q;
        $data = Mesjid::select('id', 'nama_mesjid', 'photo_mesjid')->where('nama_mesjid', 'LIKE',  "%{$cari}%")->get();

        $ress = json_decode($data);

        $g = array();
        $f = array();
        foreach ($ress as $d) {
            $g['name'] = $d->nama_mesjid;
            $g['photo'] = $d->photo_mesjid;
            $g['id'] = $d->id;
            $f[] = $g;
        }
        if ($ress != null) {
            return $f;
        } else {
            return '[]';
        }
    }


    public function getEventHome(Request $request)
    {
        $event = Event::whereDate('event.created_at', Carbon::yesterday())->orWhereDate('event.created_at', Carbon::today())
            ->join('mesjid', 'event.id_mesjid', '=', 'mesjid.id')->select(
                'event.id',
                'event_name',
                'deskripsi',
                'jadwal',
                'photo_event',
                'nama_mesjid',
                'alamat_lengkap',
                'lat',
                'lng'
            )->get();

        $f = array();
        $j = 0;
        $d = array();
        foreach ($event as $sing) {
            $ustad = Ustad::where('ustad_event_id', '=', $sing->id)->join('users', 'ustad.user_id', '=', 'users.id')
                ->select('nama_ustad', 'ustad.role', 'photo')->get();
            $i = 0;
            foreach ($ustad as $us) {
                $d[$i]['name_ustad'] = $us->nama_ustad;
                $d[$i]['role'] = $us->role;
                $d[$i]['photo'] = $us->photo;
                $i++;
            }
            $f[$j]['data'] = $sing;
            $f[$j]['ustad'] = $d;
            unset($d);
            $d = array();
            $j++;
        }
        return response()->json([
        'status'=> true,
        'messages'=> 'pesan',
        'data'=> $f
        ]);
    }
}
