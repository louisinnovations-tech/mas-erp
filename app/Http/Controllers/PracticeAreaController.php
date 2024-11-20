<?php

namespace App\Http\Controllers;

use App\Models\Advocate;
use App\Models\PracticeArea;
use Illuminate\Http\Request;

class PracticeAreaController extends Controller
{
    public function index()
    {
        if(\Auth::user()->type == 'company')
        {
            $practiceAreas = PracticeArea::get();

            return view('practiceArea.index', compact('practiceAreas'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(\Auth::user()->type == 'company')
        {
            return view('practiceArea.create');
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->type == 'company')
        {

            $validator = \Validator::make(
                $request->all(), 
                [
                    'name' => 'required',
                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $awardtype             = new PracticeArea();
            $awardtype->name       = $request->name;
            $awardtype->save();

            return redirect()->route('practice-area.index')->with('success', __('PracticeArea  successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        $practiceArea = PracticeArea::find($id);
        if(\Auth::user()->type == 'company')
        {
            return view('practiceArea.edit', compact('practiceArea'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $id)
    {
        $practiceArea = PracticeArea::find($id);
        if(\Auth::user()->type == 'company')
        {
            $validator = \Validator::make(
                $request->all(), 
                [
                    'name' => 'required',
                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $practiceArea->name = $request->name;
            $practiceArea->save();

            return redirect()->route('practice-area.index')->with('success', __('PracticeArea successfully updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {

        if(\Auth::user()->type == 'company')
        {
            $practiceArea = PracticeArea::find($id);
            $data      = Advocate::whereJsonContains('practice_areas', $practiceArea->id)->first();
            if(!empty($data))
            {
                return redirect()->back()->with('error', __('this practice area is already use so please transfer or delete this practice area related data.'));
            }


            $practiceArea->delete();

            return redirect()->route('award-type.index')->with('success', __('AwardType successfully deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
