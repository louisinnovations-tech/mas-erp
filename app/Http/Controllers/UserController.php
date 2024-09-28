<?php

namespace App\Http\Controllers;

use App\Models\Advocate;
use App\Models\Cases;
use App\Models\group;
use App\Models\PointOfContacts;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use App\Models\SalaryType;
use App\Models\Employee;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (Auth::user()->can('manage member') || Auth::user()->can('manage user')) {

            $users = User::where('created_by', '=', Auth::user()->creatorId())->where('type', '!=', 'advocate')->where('type', '!=', 'client')->get();

            $user_details = UserDetail::get();

            return view('users.index', compact('users', 'user_details'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function userList()
    {

        if (Auth::user()->can('manage member') || Auth::user()->can('manage user')) {

            $users = User::where('created_by', '=', Auth::user()->creatorId())->where('type', '!=', 'advocate')->get();
            $user_details = UserDetail::get();

            return view('users.list', compact('users', 'user_details'));
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

        if ((Auth::user()->can('create member') || Auth::user()->can('create user')) || (Auth::user()->can('manage support') || Auth::user()->can('manage crm'))) {
            $users = User::where('created_by', Auth::user()->creatorId())->orWhere('id', Auth::user()->id)->get()->pluck('referral_id', 'id');
            $roles = Role::where('created_by', Auth::user()->creatorId())->where('name', '!=', 'Advocate')->where('name', '!=', 'client')->get()->pluck('name', 'id');
            return view('users.create', compact('roles', 'users'));
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
        if (Auth::user()->can('create member') || Auth::user()->can('create user')) {

            if (Auth::user()->type == 'company') {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'name' => 'required|max:120',
                        'email' => 'required|email|unique:users',
                        'role' => 'required',
                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                $user = Auth::user();

                $enableLogin       = 0;
                if (!empty($request->password_switch) && $request->password_switch == 'on') {
                    $enableLogin   = 1;
                    $validator = \Validator::make(
                        $request->all(),
                        ['password' => 'required|min:6']
                    );

                    if ($validator->fails()) {
                        return redirect()->back()->with('error', $validator->errors()->first());
                    }
                }
                $userpassword               = $request->input('password');

                $user = new User();
                $user['name'] = $request->name;
                $user['email'] = $request->email;
                $user['password'] = !empty($userpassword) ? \Hash::make($userpassword) : null;
                $user['lang'] = 'en';
                $user['created_by'] = Auth::user()->creatorId();
                $user['email_verified_at'] = date('Y-m-d H:i:s');
                $user['is_enable_login'] = $enableLogin;

                $role_r = Role::findById($request->role);
                $user->assignRole($role_r);
                $user['type'] = $role_r->name;

                $user->save();

                $detail = new UserDetail();
                $detail->user_id = $user->id;
                $detail->save();

                return redirect()->route('users.index')->with('success', __('Member successfully created.'));
            } else {

                $validator = Validator::make(
                    $request->all(),
                    [
                        'name' => 'required|max:120',
                        'email' => 'required|email|unique:users',
                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                $enableLogin       = 0;
                if (!empty($request->password_switch) && $request->password_switch == 'on') {
                    $enableLogin   = 1;
                    $validator = \Validator::make(
                        $request->all(),
                        ['password' => 'required|min:6']
                    );

                    if ($validator->fails()) {
                        return redirect()->back()->with('error', $validator->errors()->first());
                    }
                }
                $userpassword               = $request->input('password');
                $user = new User();
                $user['name'] = $request->name;
                $user['email'] = $request->email;
                $user['password'] = !empty($userpassword) ? \Hash::make($userpassword) : null;
                $user['lang'] = 'en';
                $user['created_by'] = Auth::user()->creatorId();

                if (Utility::settings()['email_verification'] == 'off') {
                    $user['email_verified_at'] = date('Y-m-d H:i:s');
                } else {
                }

                $role_r = Role::findByName('company');
                $user->assignRole($role_r);
                $user['type'] = 'company';

                $user->save();

                $detail = new UserDetail();
                $detail->user_id = $user->id;
                $detail->save();

                //create company default roles
                $user->MakeRole($user->id);

                return redirect()->route('users.index')->with('success', __('Member successfully created.'));
            }
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
        if (Auth::user()->can('show member')) {

            $user_detail = UserDetail::where('user_id', $user_id)->first();

            if ($user_detail) {
                $data = explode(',', $user_detail->my_group);
                $my_groups = group::whereIn('id', $data)->get()->pluck('name');
                return view('users.view', compact('my_groups'));
            }
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
        
        $eId        = $id;
        //Branchges
        $branches = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $branches->prepend('Select Branch', '');
        //Department
        $department = Department::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $department->prepend('Select Department', '');
        //Designation
        $designation = Designation::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $designation->prepend('Select Designation', '');
        //SalaryType
        $salaryType = SalaryType::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $salaryType->prepend('Select Type', '');

        $employeeDetails = Employee::where('user_id', $id)->first();
        $employeesId  = \Auth::user()->employeeIdFormat(!empty($employeeDetails->employee_id) ? $employeeDetails->employee_id : '');
        $departmentData  = Department::where('created_by', \Auth::user()->creatorId())->where('branch_id', $employeeDetails->branch_id)->get()->pluck('name', 'id');
        //  dd($departmentData);

        

        $user = User::find($id);
        $user_detail = UserDetail::where('user_id', $user->id)->first();
        $roles = Role::where('created_by', '=', $user->creatorId())->get()->pluck('name', 'id');
        $advocate = $contacts = [];
        $employee = [];

        $client = [];
        $members = [];
        $cases = [];
        $referances = User::where('created_by', Auth::user()->creatorId())->orWhere('id', Auth::user()->id)->get();
        if (Auth::user()->type == 'company') {
            $employee = User::where('created_by', $id)
                ->where('type', '!=', 'advocate')
                ->where('type', '!=', 'client')
                ->get();


            $members = User::where('created_by', $id)->where('type', '!=', 'advocate')->where('type', '!=', 'client')->get();
            $client = User::where('created_by', $id)->where('type', '==', 'client')->get();
            $cases = Cases::where('created_by', $id)->get();
        }

        if (Auth::user()->type == 'advocate') {
            $advocate = Advocate::where('user_id', $user->id)->first();
            $contacts = PointOfContacts::where('advocate_id', $advocate->id)->get();
        }

        //return view('users.edit', compact('user', 'referances', 'roles', 'user_detail', 'advocate', 'contacts', 'employee', 'members', 'client', 'cases'));
        return view('users.edit', compact('user', 'departmentData', 'employeesId', 'branches', 'employee', 'department', 'designation', 'salaryType', 'referances', 'roles', 'user_detail', 'advocate', 'contacts', 'members', 'client', 'cases', 'employeeDetails'));
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

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:120',
                'email' => 'required|email',
            ]
        );
        if (!empty($request->mobile_number)) {

            $validator = Validator::make(
                $request->all(),
                [
                    'mobile_number' => 'required|numeric',
                ]
            );
        }

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $user = User::find($id);

        if ($user) {
            if (Auth::user()->type == 'advocate') {

                $adv = Advocate::where('user_id', $user->id)->first();

                if ($adv) {
                    if (!empty($request->point_of_contacts)) {
                        foreach ($request->point_of_contacts as $items) {
                            foreach ($items as $item) {

                                if (empty($item) && $item < 0) {
                                    $msg['flag'] = 'error';
                                    $msg['msg'] = __('Please enter your contacts');

                                    return redirect()->back()->with('error', $msg);
                                }
                            }
                            $validator = Validator::make(
                                $items,
                                [
                                    'contact_name' => 'required',
                                    'contact_email' => 'required',
                                    'contact_phone' => 'required|numeric',
                                    'contact_designation' => 'required',
                                ]
                            );
                        }
                    }

                    if ($validator->fails()) {
                        $messages = $validator->getMessageBag();

                        return redirect()->back()->with('error', $messages->first());
                    }

                    $advocate = Advocate::find($adv->id);
                    $userAdd = $advocate->getAdvUser($advocate->user_id);

                    if ($userAdd->email != $request->email) {

                        $users = User::where('email', $request->email)->first();
                        if (!empty($users)) {
                            return redirect()->back()->with('error', __('Email address already exist.'));
                        }
                    }

                    $advocate['phone_number'] = !empty($request->phone_number) ? $request->phone_number : NULL;
                    $advocate['father_name'] = $request->father_name;
                    $advocate['age'] = !empty($request->age) ? $request->age : NULL;
                    $advocate['company_name'] = $request->company_name;
                    $advocate['website'] = $request->website;
                    $advocate['tin'] = $request->tin;
                    $advocate['gstin'] = $request->gstin;
                    $advocate['pan_number'] = $request->pan_number;
                    $advocate['hourly_rate'] = !empty($request->hourly_rate) ? $request->hourly_rate : NULL;
                    $advocate['ofc_address_line_1'] = $request->ofc_address_line_1;
                    $advocate['ofc_address_line_2'] = $request->name;
                    $advocate['ofc_country'] = $request->ofc_country;
                    $advocate['ofc_state'] = !empty($request->ofc_state) ? $request->ofc_state : NULL;
                    $advocate['ofc_city'] = $request->ofc_city;
                    $advocate['ofc_zip_code'] = !empty($request->ofc_zip_code) ? $request->ofc_zip_code : NULL;
                    $advocate['home_address_line_1'] = $request->home_address_line_1;
                    $advocate['home_address_line_2'] = $request->home_address_line_2;
                    $advocate['home_country'] = $request->home_country;
                    $advocate['home_state'] = $request->home_state;
                    $advocate['home_city'] = $request->home_city;
                    $advocate['home_zip_code'] = !empty($request->home_zip_code) ? $request->home_zip_code : NULL;
                    $advocate->save();

                    $userAdd->name = $request->name;
                    $userAdd->email = $request->email;

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

                        $userAdd->avatar = $fileNameToStore;
                    }

                    $userAdd->save();

                    foreach (json_decode($request->old_contacts, true) as $key => $value) {
                        $contacts = PointOfContacts::find($value['id']);
                        $contacts->delete();
                    }

                    if (!empty($request->point_of_contacts)) {
                        foreach ($request->point_of_contacts as $key => $value) {
                            $contacts = new PointOfContacts();

                            $contacts['advocate_id'] = $advocate->id;
                            $contacts['contact_name'] = $value['contact_name'];
                            $contacts['contact_email'] = $value['contact_email'];
                            $contacts['contact_phone'] = $value['contact_phone'];
                            $contacts['contact_designation'] = $value['contact_designation'];

                            $contacts->save();
                        }
                    }
                    return redirect()->back()->with('success', __('Successfully Updated!'));
                } else {
                    return redirect()->back()->with('error', __('Advocate not found.'));
                }
            } else {
                $user['name'] = $request->name;
                $user['email'] = $request->email;

                if ($request->hasFile('profile')) {
                    if (Auth::user()->type == 'company') {
                        $filenameWithExt = $request->file('profile')->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension = $request->file('profile')->getClientOriginalExtension();
                        $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                        $settings = Utility::Settings();
                        $url = '';
                        $dir = 'uploads/profile/';
                        $path = Utility::upload_file($request, 'profile', $fileNameToStore, $dir, []);

                        if ($path['flag'] == 1) {
                            $url = $path['url'];
                        } else {
                            return redirect()->route('users.index', Auth::user()->id)->with('error', __($path['msg']));
                        }

                        $user->avatar = $fileNameToStore;
                    } else {
                        $dir        = 'uploads/profile/';
                        $file_path = $dir . $user['avatar'];


                        $filenameWithExt = $request->file('profile')->getClientOriginalName();
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension = $request->file('profile')->getClientOriginalExtension();
                        $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                        $settings = Utility::Settings();
                        $url = '';
                        $dir = 'uploads/profile/';
                        $path = Utility::upload_file($request, 'profile', $fileNameToStore, $dir, []);

                        if ($path['flag'] == 1) {
                            $url = $path['url'];
                        } else {
                            return redirect()->route('users.index', Auth::user()->id)->with('error', __($path['msg']));
                        }

                        $user->avatar = $fileNameToStore;
                    }
                }

                $user->update();

                $detail = UserDetail::where('user_id', $user->id)->first();

                $detail->mobile_number = !empty($request->mobile_number) ? $request->mobile_number : null;
                $detail->whats_app_number = !empty($request->whats_app_number) ? $request->whats_app_number : null;
                $detail->address = $request->address;
                $detail->city = $request->city;
                $detail->state = $request->state;
                $detail->building_number = $request->building_number;
                $detail->street_number = $request->street_number;
                $detail->zone_number = $request->zone_number;
                $detail->qid_number = $request->qid_number;
                $detail->passport_number = $request->passport_number;
                $detail->land_phone = $request->land_phone;
                $detail->extension_number = $request->extension_number;
                $detail->profession = $request->profession;
                $detail->department = $request->department;
                $detail->language = $request->language;
                $detail->zip_code = !empty($request->zip_code) ? $request->zip_code : null;
                $detail->landmark = $request->landmark;
                $detail->about = $request->about;

                $detail->save();
                if (Auth::user()->type == 'company') {
                    return redirect()->route('users.edit', Auth::user()->id)->with('success', __('Successfully Updated!'));
                } else if (Auth::user()->can('manage crm') || Auth::user()->can('manage support') || Auth::user()->type == 'client') {
                    return redirect()->back()->with('success', __('Profile updated.'));
                }

                return redirect()->route('users.index')->with('success', __('Successfully Updated!'));
            }
        } else {
            return redirect()->back()->with('error', __('Member not found.'));
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
                return redirect()->back()->with('error', __('You can\'t delete yourself.'));
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




    public function verify($user_id)
    {

        $user = User::where('id', $user_id)->first();
        if ($user) {
            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->save();
            return redirect()->back()->with('success', __('Email verify!'));
        }
    }
    public function detail($user_id)
    {

        $user = User::where('id', $user_id)->first();
        $employee = User::where('created_by', '=', $user_id)
            ->where('type', '!=', 'client')
            ->get();
        $user_detail = UserDetail::where('user_id', $user->id)->first();

        $members = User::where('created_by', $user_id)->where('type', '!=', 'advocate')->where('type', '!=', 'client')->get();
        $client = User::where('created_by', $user_id)->where('type', '==', 'client')->get();
        $cases = Cases::where('created_by', $user_id)->get();
        return view('users.detail', compact('user', 'user_detail', 'employee', 'members', 'client', 'cases'));
    }

    public function LoginManage($id)
    {
        $eId        = Crypt::decrypt($id);
        $user = User::find($eId);
        if ($user->is_enable_login == 1) {
            $user->is_enable_login = 0;
            $user->save();
            return redirect()->back()->with('success', __('User login disable successfully.'));
        } else {
            $user->is_enable_login = 1;
            $user->save();
            return redirect()->back()->with('success', __('User login enable successfully.'));
        }
    }

    public function userPassword($id)
    {
        $eId = Crypt::decrypt($id);
        $user = User::find($eId);

        return view('reset', compact('user'));
    }

    public function userPasswordReset(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'password' => 'required|confirmed|same:password_confirmation',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $user = User::where('id', $id)->first();
        $user->forceFill([
            'password' => Hash::make($request->password),
            'is_enable_login' => 1,
        ])->save();

        return redirect()->route('users.index')->with(
            'success',
            'User Password successfully updated.'
        );
    }
}
