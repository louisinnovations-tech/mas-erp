<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class FaqController extends Controller
{

    public function index()
    {
        $user = \Auth::user();

        if(Auth::user()->can('manage support') )
        {

            $faqs = Faq::get();
            return view('faq.index', compact('faqs'));
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

        if(Auth::user()->can('manage support'))
        {
            return view('faq.create');
        }else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
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
            ];
            $request->validate($validation);

            $post = [
                'title' => $request->title,
                'description' => $request->description,
            ];

            Faq::create($post);
            return redirect()->route('faq.index')->with('success',  __('Faq created successfully'));
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

        if(Auth::user()->can('manage support'))
        {
            $faq = Faq::find($id);
            return view('faq.edit', compact('faq'));
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

        if(Auth::user()->can('manage support'))
        {
            $faq = Faq::find($id);
            $faq->update($request->all());
            return redirect()->route('faq.index')->with('success', __('Faq updated successfully'));
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
            $faq = Faq::find($id);
            $faq->delete();
            return redirect()->route('faq.index')->with('success', __('Faq deleted successfully'));
        }else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
