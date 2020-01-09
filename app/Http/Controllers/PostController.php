<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use Validator;
use GrofGraf\LaravelPDFMerger\Facades\PDFMergerFacade;


class PostController extends Controller
{


    // public function create(){

    //     return view('posts');

    // }



    public function store(Request $request){
        
        $rules = [

            'title' => 'required',
            'desc' => 'required',
            'file' => 'required|mimetypes:application/pdf|max:10000'

        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){

            return response()->json([
              "status" => "error",
              "errors" => $validator->errors()
            ]);

        }

        
        $post = new Post;
        $post->title = $request->title;
        $post->desc = $request->desc;
        if($request->hasFile('file')){

            $fileName = $request->file('file')->getClientOriginalName();

            $request->file('file')->move(public_path('files/') , $fileName);

            $post->file = $fileName;
        }
        
        $post->save();

        return response()->json([

            'status' => 'success',
            'data'   => $post

        ]);

    }


    public function merge(){

        $posts = Post::get(['file']);

        $merger = \PDFMerger::init();
        foreach($posts as $post){

            $merger->addPathToPDF(public_path('files/') . $post['file'], 'all');

        }

        $merger->merge();

        $merger->setFileName('merger.pdf');

        $merger->download();

        $merger->save(public_path('files/') . 'merger.pdf');

        return response()->json([

            'status' => 'success'

        ]);
    }

}