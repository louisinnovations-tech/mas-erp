<?php

namespace App\Http\Controllers;

use App\Exports\ClientExport;
use App\Imports\ClientsImport;
use App\Models\Advocate;
use App\Models\group;
use App\Models\Order;
use App\Models\Hearing;
use App\Models\PointOfContacts;
use App\Models\User;
use App\Models\Bill;
use App\Models\Client;
use App\Models\Department;
use App\Models\Fee;
use App\Models\UserDetail;
use App\Models\Utility;
use Database\Seeders\UserSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (Auth::user()->can('manage member') || Auth::user()->can('manage user')) {

            $users = User::where('created_by', '=', Auth::user()->creatorId())
                    ->where('type','client')
                    ->orderBy('created_at','desc')
                    ->get();

            $user_details = UserDetail::get();

            return view('client.index', compact('users', 'user_details'));

        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));

        }

    }

    public function userList()
    {

        if (Auth::user()->can('manage member') || Auth::user()->can('manage user')) {

            $users = User::where('created_by', '=', Auth::user()->creatorId())->where('type','client')->get();
            $user_details = UserDetail::get();

            return view('client.list', compact('users', 'user_details'));

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
        if (Auth::user()->can('create member') || Auth::user()->can('create user')) {

            $roles = Role::where('created_by', Auth::user()->creatorId())->where('id', '!=', Auth::user()->id)->where('name','!=','Advocate')->get()->pluck('name', 'id');

            return view('client.create', compact('roles'));
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
        if (Auth::user()->can('create client')) {

            $request->validate(
                [
                    'name' => 'required|max:120',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required|min:8',
                    'mobile_number' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                ]
            );

            $user = Auth::user();
            $user = new User();
            $user['name'] = $request->name;
            $user['email'] = $request->email;
            $user['password'] = Hash::make($request->password);
            $user['lang'] = 'en';
            $user['created_by'] = Auth::user()->creatorId();
            $user['email_verified_at'] = date('Y-m-d H:i:s');
            $user['is_enable_login'] = $request->password_switch == 'on';

            $user->assignRole('client');
            $user['type'] = 'client';

            if ($request->hasFile('profile')) {
                $filenameWithExt = $request->file('profile')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('profile')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $dir = 'uploads/profile/';
                $path = Utility::upload_file($request, 'profile', $fileNameToStore, $dir, []);

                if ($path['flag'] == 1) {
                    $url = $path['url'];
                } else {
                    return redirect()->route('users.index', Auth::user()->id)->with('error', __($path['msg']));
                }

                $user->avatar = $fileNameToStore;
            }


            $user->save();

            $detail = new UserDetail();
            $detail->user_id = $user->id;
            $detail['mobile_number']         = $request->mobile_number;
            $detail['whats_app_number']         = $request->whats_app_number;
            $detail['address']         = $request->address;
            $detail['building_number']         = $request->building_number;
            $detail['street_number']         = $request->street_number;
            $detail['zone_number']         = $request->zone_number;
            $detail['qid_number']         = $request->qid_number;
            $detail['city']         = $request->city;
            $detail['passport_number']         = $request->passport_number;
            $detail['language']         = $request->language;
            $detail->save();

            return redirect()->route('client.index')->with('success', __('Member successfully created.'));

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
    public function show($user_id)
    {


        $user= User::where('id', $user_id)->first();

        if ($user) {

            $case = DB::table("cases")
                        ->select("cases.*")
                        ->get();
            $cases=[];
            foreach($case as $value)
            {
               $data=json_decode($value->your_party_name);
               foreach($data as $key => $val)
               {
                   if(isset($val->clients)&& $val->clients ==$user->id)
                   {
                        $hearings = Hearing::where('case_id',$value->id)->get();
                        $value->hearings=$hearings;
                        $cases[$value->id]=$value;
                   }
               }
            }
            $bills = Bill::where('bill_to',$user->id)->get();
             $fees = Fee::where('member',$user->id)->get();
            return view('client.view', compact('user','cases','bills','fees'));
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
        $user = User::find($id);
        if($user){
            if ((Auth::user()->type == 'client' && Auth::user()->id == $user->id )|| Auth::user()->can('edit client')) {
                $user_detail = UserDetail::where('user_id', $user->id)->first();
                $roles = Role::where('created_by', '=', $user->creatorId())->get()->pluck('name', 'id');
                $advocate = $contacts = [];
                if(Auth::user()->type == 'advocate'){
                    $advocate = Advocate::where('user_id',$user->id)->first();
                    $contacts = PointOfContacts::where('advocate_id',$advocate->id)->get();
                }
                return view('client.edit', compact('user', 'roles', 'user_detail','advocate','contacts'));
            }
        }
        return redirect()->back()->with('error', __('Client not found.'));
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
        $request->validate(
            [
                'name' => 'required|max:120',
                'email' => 'required|email|unique:users,email,'.$id,
                'mobile_number' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            ]
        );

        $user = User::find($id);

        if ($user) {
            if ((Auth::user()->type == 'client' && Auth::user()->id == $user->id )|| Auth::user()->can('edit client')) {

                $user->name = $request->name;
                $user->email = $request->email;

                if ($request->hasFile('profile')) {
                    $filenameWithExt = $request->file('profile')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('profile')->getClientOriginalExtension();
                    $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                    $dir = 'uploads/profile/';
                    $path = Utility::upload_file($request, 'profile', $fileNameToStore, $dir, []);

                    if ($path['flag'] == 1) {
                        $url = $path['url'];
                    } else {
                        return redirect()->route('users.index', Auth::user()->id)->with('error', __($path['msg']));
                    }

                    $user->avatar = $fileNameToStore;
                }

                $user->is_enable_login = $request->password_switch == 'on';
                $user->save();

                $detail = $user->userDetail;
                $detail['mobile_number']         = $request->mobile_number;
                $detail['whats_app_number']         = $request->whats_app_number;
                $detail['address']         = $request->address;
                $detail['building_number']         = $request->building_number;
                $detail['street_number']         = $request->street_number;
                $detail['zone_number']         = $request->zone_number;
                $detail['qid_number']         = $request->qid_number;
                $detail['city']         = $request->city;
                $detail['passport_number']         = $request->passport_number;
                $detail['language']         = $request->language;
                $detail->save();

                return redirect()->back()->with('success', __('Successfully Updated!'));

            }else{
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Client not found.'));

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
        if (Auth::user()->can('delete member') || Auth::user()->can('delete user')) {
            $user = User::find($id);
            $detail = UserDetail::where('user_id', $user->id)->first();

            if ($user->created_by != Auth::user()->creatorId()) {
                return redirect()->back()->with('error', __('You cant delete yourself.'));
            } else {
                if ($user && $detail) {
                    $user->delete();
                    $detail->delete();

                    $data = explode(',', $detail->my_group);
                    $my_groups = group::whereIn('id', $data)->get();

                    foreach ($my_groups as $key => $value) {
                        if (str_contains($value->members, $detail->user_id)) {
                            $value->members = trim($value->members, $detail->user_id);
                            $value->save();
                        }
                    }

                    return redirect()->back()->with('success', __('Member deleted successfully.'));
                }
            }
        } else {
            return redirect()->back()->with('error', __('Member not found.'));
        }
    }

    public function changeMemberPassword(Request $request, $id)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'password' => 'required|same:confirm_password',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $objUser = User::find($id);
        $objUser->password = Hash::make($request->password);
        $objUser->save();

        return redirect()->back()->with('success', __('Password updated successfully.'));

    }

    public function companyPassword($id)
    {
        $eId   = Crypt::decrypt($id);
        $user  = User::find($eId);

        $employee = User::where('id', $eId)->first();

        return view('users.reset', compact('user', 'employee'));
    }

    public function fileImport()
    {
        return view('client.import');
    }

    public function import(Request $request)
    {
        $rules = [
            'file' => 'required|mimes:csv,txt',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $clients = (new ClientsImport())->toArray(request()->file('file'))[0];

        $totalcase = count($clients) - 1;
        $errorArray    = [];

        for ($i = 1; $i <= count($clients) - 1; $i++) {

            $deal          = $clients[$i];
            $check_user = User::where('type', '=', 'client')->where('created_by', '=', Auth::user()->creatorId())->first();
            if(!$check_user){

                $dealData = new User();
                $dealData->name = $deal[0];
                $dealData->email = $deal[1];
                $dealData->password = Hash::make($deal[2]);
                $dealData->lang = 'en';
                $dealData->created_by = Auth::user()->creatorId();
                $dealData->email_verified_at = date('Y-m-d H:i:s');
                $dealData->created_by = Auth::user()->id;
                $dealData->type = 'client';
                $dealData->save();

                $dealData->assignRole('client');

                $detail = new UserDetail();
                $detail->user_id = $dealData->id;
                $detail->save();
            }else{
                $errorArray[] = $i;
                $data['status'] = 'error';
                $data['msg'] = $totalcase . '  ' . __($check_user->email.' Already exist.');
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

    public function exportFile()
    {
        $name = 'clients_' . date('Y-m-d i:h:s');
        $data = Excel::download(new ClientExport(), $name . '.xlsx');
        ob_end_clean();
        return $data;
    }

    public function getdepartment(Request $request)
    {
        if ($request->branch_id == 0) {
            $departments = Department::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        } else {
            $departments = Department::where('created_by', '=', \Auth::user()->creatorId())->where('branch_id', $request->branch_id)->get()->pluck('name', 'id')->toArray();
        }

        return response()->json($departments);
    }

    public function json(Request $request)
    {
        $designations = Designation::where('department', $request->department_id)->get()->pluck('name', 'id')->toArray();

        return response()->json($designations);
    }
}
