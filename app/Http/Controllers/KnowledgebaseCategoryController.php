<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Knowledgebasecategory;
use Illuminate\Support\Facades\Auth;
class KnowledgebaseCategoryController extends Controller
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

            $knowledges_category = Knowledgebasecategory::get();

            return view('knowledgecategory.index', compact('knowledges_category'));
        }else{
             return redirect()->back()->with('error', __('Permission denied.'));
        }
        return view('knowledgecategory.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('knowledgecategory.create');
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
            ];
            $request->validate($validation);

            $post = [
                'title' => $request->title,
            ];

            Knowledgebasecategory::create($post);
            return redirect()->route('knowledgecategory')->with('success',  __('KnowledgeBase Category created successfully'));
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

        if(Auth::user()->can('manage support') )
        {
            $knowledge_category = Knowledgebasecategory::find($id);
            return view('knowledgecategory.edit', compact('knowledge_category'));
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
            $knowledge_category = Knowledgebasecategory::find($id);
            $knowledge_category->update($request->all());
            return redirect()->route('knowledgecategory')->with('success', __('KnowledgeBase Category updated successfully'));
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
            $knowledge_category = Knowledgebasecategory::find($id);
            $knowledge_category->delete();
            return redirect()->route('knowledgecategory')->with('success', __('KnowledgeBase Category deleted successfully'));
        }else{
             return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

}
