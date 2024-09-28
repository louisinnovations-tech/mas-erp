<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Knowledge;
use App\Models\Knowledgebasecategory;
use Illuminate\Support\Facades\Auth;
class KnowledgeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = \Auth::user();

        if(Auth::user()->can('manage support') )
        {

            $knowledges = Knowledge::get();

            return view('knowledge.index', compact('knowledges'));
        }else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = Knowledgebasecategory::get();
        return view('knowledge.create',compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if(Auth::user()->can('manage support') )
        {
            $validation = [
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required'],
                'category' => ['required', 'string', 'max:255'],
            ];
            $request->validate($validation);

            $post = [
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
            ];

            Knowledge::create($post);
            return redirect()->route('knowledge')->with('success',  __('Knowledge created successfully'));
        }else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $userObj = \Auth::user();

        if(Auth::user()->can('manage support') )
        {
            $knowledge = Knowledge::find($id);
            $category = Knowledgebasecategory::get();
            return view('knowledge.edit', compact('knowledge','category'));
        }else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
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

        if(Auth::user()->can('manage support') )
        {
            $knowledge = Knowledge::find($id);
            $knowledge->update($request->all());
            return redirect()->route('knowledge')->with('success', __('Knowledge updated successfully'));
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        if(Auth::user()->can('manage support') )
        {
            $knowledge = Knowledge::find($id);
            $knowledge->delete();
            return redirect()->route('knowledge')->with('success', __('Knowledge deleted successfully'));
        }else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

}
