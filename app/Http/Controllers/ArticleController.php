<?php

namespace App\Http\Controllers;

use App\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Auth;
use Validator;
use Redirect;

class ArticleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        return view('pages.dashboard.articles', [
            'user'     => $user,
            'published' => $user->articles()->where('status' , 'published')->get(),
            'drafts' => $user->articles()->where('status', 'draft')->get(),
            'pending' => $user->articles()->where('status', 'pending')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("pages.dashboard.createArticle")->with('user', Auth::user());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Auth::user();
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:50',
            'content' => 'required'
        ]);

        if ($validator->fails()) {
            return back()->withInput()
                    ->withErrors($validator);
        } else {
            // store
            $article = new Article();
            $article->title  = Input::get('title');
            $article->content  = Input::get('content');
            $article->author = Auth::user()->id;
            
            if(!$request->has('draft'))
                $article->status = 'pending';

            $article->save();

            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function edit(Article $article)
    {
        // check if article blongs to user
        return view('pages.dashboard.editArticle', [
            'user'      => Auth::user(),
            'article'   => $article,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article)
    {
        // check if article blongs to user
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:50',
            'content' => 'required'
        ]);

        if ($validator->fails()) {
            return back()->withInput()
                    ->withErrors($validator);
        } else {
            // update
            $article->title  = Input::get('title');
            $article->content  = Input::get('content');
            
            if(!$request->has('draft'))
                $article->status = 'pending';

            $article->save();

            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        $article->delete();
        return redirect('backoffice/articles');
    }
}
