<?php

namespace App\Http\Controllers;

use App\Exports\AdvocatesExport;
use App\Imports\AdvocatesImport;
use App\Models\Advocate;
use App\Models\Bill;
use App\Models\Cases;
use App\Models\PointOfContacts;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\PracticeArea;


class AdvocateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->can('manage advocate') || Auth::user()->type === 'client') {

            $advocates = Advocate::where('created_by', Auth::user()->creatorId())
                ->with('getAdvUser')
                ->orderBy('created_at','desc')
                ->get();

            return view('advocate.index', compact('advocates'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        if (Auth::user()->can('create advocate')) {
            $practiceAreas = PracticeArea::pluck('name', 'id');

            return view('advocate.create', compact('practiceAreas'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
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
        if(Auth::user()->can('create advocate')){
            $users = User::where('email',$request->email)->first();
            if (!empty($users)) {
                return redirect()->back()->with('error', __('Email address already exist.'));
            }

            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:120',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => ['required', 'min:8'],
                    'phone_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
                ]
            );


            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $user = Auth::user();

            $new_user = User::create(
                [
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'type' => 'advocate',
                    'lang' => 'en',
                    'avatar' => '',
                    'created_by' => $user->creatorId(),
                    'email_verified_at' => now(),
                    'is_enable_login' => $request->password_switch == 'on'
                ]
            );
            $new_user->assignRole('advocate');

            $advocate = new Advocate();
            $advocate['user_id']              = $new_user->id;
            $advocate['phone_number']         = $request->phone_number;
            $advocate['age']                  = $request->age;
            $advocate['company_name']         = $request->company_name;
            $advocate['bank_details']         = $request->bank_details;
            $advocate['ofc_address_line_1']   = $request->ofc_address_line_1;
            $advocate['ofc_address_line_2']   = $request->name;
            $advocate['ofc_country']          = $request->ofc_country;
            $advocate['ofc_state']            = $request->ofc_state;
            $advocate['ofc_city']             = $request->ofc_city;
            $advocate['ofc_zip_code']         = $request->ofc_zip_code;
            $advocate['home_address_line_1']  = $request->home_address_line_1;
            $advocate['home_address_line_2']  = $request->home_address_line_2;
            $advocate['home_country']         = $request->home_country;
            $advocate['home_state']           = $request->home_state;
            $advocate['home_city']            = $request->home_city;
            $advocate['home_zip_code']        = $request->home_zip_code;
            $advocate['department']           = $request->department;
            $advocate['profile_link']         = $request->profile_link;
            $advocate['nationality']         = $request->nationality;
            $advocate['languages']         = $request->languages;
            $advocate->practice_areas = json_encode($request->practice_areas);
            $advocate['created_by']           = $user->creatorId();
            $advocate->save();



            return redirect()->route('advocate.index')->with('success', __('Advocate successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
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
        if (Auth::user()->can('view advocate')) {

            $cases = [];
            $cases_data = Cases::get();

            foreach ($cases_data as $key => $case) {

                if (str_contains($case->advocates, $id)) {
                    $cases[$key]['id']          = $case->id;
                    $cases[$key]['court']       = $case->court;
                    $cases[$key]['case_number'] = $case->case_number;
                    $cases[$key]['title']       = $case->title;
                    $cases[$key]['advocates']   = $case->advocates;
                    $cases[$key]['year']        = $case->year;
                    $cases[$key]['filing_date'] = $case->filing_date;
                }
            }

            return view('advocate.view', compact('cases'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::user()->can('edit advocate') || Auth::user()->id == $id) {

            $advocate = Advocate::where('user_id',$id)->first();
            if($advocate){
                $contacts = PointOfContacts::where('advocate_id',$advocate->id)->get();
                $practiceAreas = PracticeArea::pluck('name', 'id');
                return view('advocate.edit',compact('advocate','contacts', 'practiceAreas'));
            }else{

                return redirect()->back()->with('error', __('Advocate not found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
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
        if (Auth::user()->can('edit advocate') || Auth::user()->id == $id) {

            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:120',
                    'email' => 'required|email',
                    'phone_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
                ]
            );


            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $advocate = Advocate::where('user_id',$id)->first();
            $userAdd = $advocate->getAdvUser;

            if ($userAdd->email != $request->email) {

                $users = User::where('email', $request->email)->first();
                if (!empty($users)) {
                    return redirect()->back()->with('error', __('Email address already exist.'));
                }
            }

            $advocate['phone_number']         = $request->phone_number;
            $advocate['age']                  = $request->age;
            $advocate['company_name']         = $request->company_name;
            $advocate['bank_details']              = $request->bank_details;
            $advocate['ofc_address_line_1'] = $request->ofc_address_line_1;
            $advocate['ofc_address_line_2'] = $request->name;
            $advocate['ofc_country'] = $request->ofc_country;
            $advocate['ofc_state'] = $request->ofc_state;
            $advocate['ofc_city'] = $request->ofc_city;
            $advocate['ofc_zip_code'] = $request->ofc_zip_code;
            $advocate['home_address_line_1'] = $request->home_address_line_1;
            $advocate['home_address_line_2'] = $request->home_address_line_2;
            $advocate['home_country'] = $request->home_country;
            $advocate['home_state'] = $request->home_state;
            $advocate['home_city'] = $request->home_city;
            $advocate['home_zip_code'] = $request->home_zip_code;
            $advocate['department']           = $request->department;
            $advocate['profile_link']         = $request->profile_link;
            $advocate['nationality']         = $request->nationality;
            $advocate['languages']         = $request->languages;
            $advocate->practice_areas = json_encode($request->practice_areas);
            $advocate->save();

            $userAdd->name = $request->name;
            $userAdd->email = $request->email;
            $userAdd->referral_id = '#' . time();
            Auth::user()->can('edit advocate') && Auth::user()->id != $id && $userAdd->is_enable_login = $request->password_switch == 'on';
            $userAdd->save();

            return redirect()->route('advocate.edit',$id)->with('success', __('Successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
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
        if (Auth::user()->can('delete advocate')) {
            try {
                $adv = Advocate::find($id);

                if ($adv) {
                    $userAdd = $adv->getAdvUser($adv->user_id);

                    if ($userAdd) {
                        $userAdd->delete();
                    }

                    $adv->delete();
                    return redirect()->route('advocate.index')->with('success', __('Advocate successfully deleted.'));
                } else {
                }
            } catch (Throwable $th) {

                return redirect()->back()->with('error', $th);
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function contacts($id)
    {
        if (Auth::user()->can('view advocate')) {
            $contacts = PointOfContacts::where('advocate_id', $id)->get();
            return view('advocate.contacts', compact('contacts'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function bills($id)
    {
        if (Auth::user()->can('view advocate')) {
            $bills = Bill::where('advocate', $id)->get();
            return view('advocate.bills', compact('bills'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function view($id)
    {
        if (Auth::user()->can('view advocate') || Auth::user()->type === 'client') {
            $advocate = Advocate::find($id);
            return view('advocate.detail', compact('advocate'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    public function manageHearings($id)
    {
        if (Auth::user()->can('view advocate')) {
            $advocate = Advocate::find($id);

            return view('advocate.hearings', compact('advocate'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function exportFile()
    {
        $name = 'advocates_' . date('Y-m-d i:h:s');
        $data = Excel::download(new AdvocatesExport(), $name . '.xlsx');
        ob_end_clean();
        return $data;
    }

    public function fileImport()
    {
        return view('advocate.import');
    }

    public function import(Request $request)
    {
        $rules = [
            'file' => 'required|mimes:csv,txt',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $clients = (new AdvocatesImport())->toArray(request()->file('file'))[0];

        $totalcase = count($clients) - 1;
        $errorArray    = [];

        for ($i = 1; $i <= count($clients) - 1; $i++) {

            $deal          = $clients[$i];
            $check_user = User::where('type', '=', 'advocate')->where('created_by', '=', Auth::user()->creatorId())->first();
            if (!$check_user) {

                $dealData = new User();
                $dealData->name = $deal[0];
                $dealData->email = $deal[1];
                $dealData->password = Hash::make($deal[2]);
                $dealData->lang = 'en';
                $dealData->created_by = Auth::user()->creatorId();
                $dealData->email_verified_at = date('Y-m-d H:i:s');
                $dealData->created_by = Auth::user()->id;
                $dealData->type = 'advocate';
                $dealData->save();

                $dealData->assignRole('advocate');

                $detail = new Advocate();
                $detail->user_id = $dealData->id;
                $detail->created_by = Auth::user()->creatorId();
                $detail->save();
            } else {
                $errorArray[] = $i;
                $data['status'] = 'error';
                $data['msg'] = $totalcase . '  ' . __($check_user->email . ' Already exist.');
            }
        }
        if (!empty($errorArray)) {
            $data['status'] = 'error';
            $data['msg'] = $totalcase . '  ' . __('Record imported fail out of' . ' ' . count($errorArray) . ' ' . 'record');
        } else {
            $data['status'] = 'success';
            $data['msg']    = __('Record successfully imported');
        }

        return redirect()->back()->with($data['status'], $data['msg']);
    }
}