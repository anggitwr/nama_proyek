<?php

namespace App\Http\Controllers;

use App\Models\siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Telegram;
use Carbon\Carbon;


class siswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = siswa::orderBy('id','desc')->paginate(3);

        return view('siswa.index')->with('data', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('siswa.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Session::flash('id', $request->id);
        Session::flash('nama', $request->nama);

        $request->validate([
            'id' => 'required|numeric|unique:siswa,id',
            'nama' => 'required',
           
        ], [
            'id.required' => 'ID wajib diisi',
            'id.numeric' => 'ID wajib dalam angka',
            'id.unique' => 'ID yang diisikan sudah ada dalam database',
            'nama.required' => 'Nama wajib diisi',
        ]);

        $data = [
            'id' => $request->id,
            'nama' => $request->nama,
        ];
        
        siswa::create($data);
        
        $currentTime = Carbon::now()->diffForHumans();
      
        $messageText = "Data baru dibuat " . $currentTime . "\n\n"; 
        $messageText .= "ID: " . $data['id'] . "\n";
        $messageText .= "Nama: " . $data['nama'];
        
        Telegram::sendMessage([
            "chat_id" => env('TELEGRAM_CHAT_ID', ''),
            "parse_mode" => "HTML",
            "text" => $messageText
        ]);
        return redirect()->to('siswa')->with('success', 'data berhasil ditambahkan');
        
       
    }

    public function teleUpdates(){
        $updates = Telegram::getUpdates();

        dd($updates);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = siswa::where('id', $id)->first();
        return view('siswa.edit')->with('data', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            
            'nama' => 'required',
           
        ], [
        
            'nama.required' => 'Nama wajib diisi',
        ]);

        $data = [
           
            'nama' => $request -> nama,
        ];
        siswa::where('id',$id)->update($data);

        $currentTime = Carbon::now()->diffForHumans();

        $messageText = "Data diedit pada " . $currentTime . "\n\n";
        $messageText .= "Nama: " . $data['nama'];
        Telegram::sendMessage([
            "chat_id"=>env('TELEGRAM_CHAT_ID', ''),
            "parse_mode"=>"HTML",
            "text"=>$messageText
        ]);
        return redirect()->to('siswa')->with('success', 'data berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        siswa::where('id', $id)->delete();
        return redirect()->to('siswa')->with('success', 'Berhasil melakukan delete data');
    }
}
