<?php

namespace App\Http\Controllers\Api;
use App\Http\Resources\DataPelangganResource;
use App\Http\Controllers\Controller;
use App\Models\DataPelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Storage;

class DataPelangganController extends Controller

{//import Facade "Storage"


    //
    public function index()
        {//get all posts
        $posts = DataPelanggan::latest()->paginate(5);

        //return collection of posts as a resource
        return new DataPelangganResource(true, 'List Data Pelanggan', $posts);
        }
        public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
             'nama_pelanggan' => 'required',
        'no_whatsapp' => 'required',
        'pesannotifikasi' => 'nullable',
         'telegram_chat_id'=> 'nullable',

        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        // $image = $request->file('image');
        // $image->storeAs('public/posts', $image->hashName());

        //create post
        $post = DataPelanggan::create([
            'nama_pelanggan' => $request->nama_pelanggan,
        'no_whatsapp' => $request->no_whatsapp,
       'pesannotifikasi' => $request->pesannotifikasi ?? '',
        'telegram_chat_id' => $request->telegram_chat_id ?? ''

        ]);

        //return response
        return new DataPelangganResource(true, 'Data Pelanggan Berhasil Ditambahkan CUY !', $post);
    }
    public function show($id)
    {
        //find post by ID
        $post = DataPelanggan::find($id);

        //return single post as a resource
        return new DataPelangganResource(true, 'Detail Data Pelanggan!', $post);
    }
    public function update(Request $request, $id)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'nama_pelanggan'     => 'required',
            'no_whatsapp'   => 'required',
            'pesannotifikasi' => 'required',
             'telegram_chat_id' => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //find post by ID
        $post = DataPelanggan::find($id);

        //check if image is not empty
        if ($request->hasFile('image')) {

            //upload image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            //delete old image
            Storage::delete('public/posts/'.basename($post->image));

            //update post with new image
            $post->update([
                'nama_pelanggan' => $request->nama_pelanggan,
        'no_whatsapp' => $request->no_whatsapp,
        'pesannotifikasi' => $request->pesannotifikasi,
        'tanggal_notifikasi' => $request->tanggal_notifikasi,
         'telegram_chat_id' => $request->telegram_chat_id,
            ]);

        } else {

            //update post without image
            $post->update([
               'nama_pelanggan' => $request->nama_pelanggan,
        'no_whatsapp' => $request->no_whatsapp,
        'pesannotifikasi' => $request->pesannotifikasi,
        'tanggal_notifikasi' => $request->tanggal_notifikasi,
        'telegram_chat_id' => $request->telegram_chat_id,
            ]);
        }

        //return response
        return new DataPelangganResource(true, 'Data Pelanggan Berhasil Diubah!', $post);
    }
    public function destroy($id)
    {
        //find post by ID
        $post = DataPelanggan::find($id);

        //delete image
        Storage::delete('public/posts/'.basename($post->image));

        //delete post
        $post->delete();

        //return response
        return new DataPelangganResource(true, 'Data Pelanggan Berhasil Dihapus!', null);
}
}

