<?php

namespace App\Http\Controllers;

use App\Models\group;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (Auth::user()->can('manage member') || Auth::user()->can('manage employee') || Auth::user()->can('manage user')) {


                $employee = User::where('created_by', '=', Auth::user()->creatorId())
                        ->where('type','employee')
                        ->orderBy('created_at','desc')
                        // ->where('super_admin_employee',1)
                        ->get();

                $user_details = UserDetail::get();

            return view('employee.index', compact('employee', 'user_details'));

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
        if (Auth::user()->can('create member') || Auth::user()->can('manage employee') || Auth::user()->can('create user')) {
            $permissions=$this->permission_arr();

            return view('employee.create',compact('permissions'));
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
        if (Auth::user()->can('create member') || Auth::user()->can('manage employee') || Auth::user()->can('create user')) {
            $validator = Validator::make(
                $request->all(), [
                    'name' => 'required|max:120',
                    'email' => 'required|email|unique:users',
                    'password' => 'required|min:8',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            // $permissions=$this->permission_arr();
            // $permission_arr=[];

            // if($request->permissions){
            //     foreach($permissions as $key => $permission)
            //     {
            //     foreach ($request->permissions as $ke => $value) {
            //             if($key==$value)
            //             {
            //                 $permission_arr[$key]=$permission;
            //             }
            //     }
            //     }
            // }else{
            //     return redirect()->back()->with('error', __('Atleast one permission is required.'));
            // }

            $user = new User();
            $user['name'] = $request->name;
            $user['email'] = $request->email;
            $user['password'] = Hash::make($request->password);
            $user->assignRole('employee');
            $user['type'] ='employee';
            // $user['type'] ='super_admin_employee';
            // $user['super_admin_employee'] = 1;
            // $user['permission_json'] = json_encode($permission_arr);
            $user['lang'] = 'en';
            $user['created_by'] = Auth::user()->creatorId();
            if (Utility::settings()['email_verification'] == 'off') {
            $user['email_verified_at'] = date('Y-m-d H:i:s');
            }
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
                    return redirect()->back()->with('error', __($path['msg']));
                }

                $user->avatar = $fileNameToStore;
            }
            $user->save();
            $detail = new UserDetail();
            $detail->user_id = $user->id;
            $detail->mobile_number = $request->mobile_number;
            $detail->land_phone = $request->land_phone;
            $detail->extension_number = $request->extension_number;
            $detail->profession = $request->profession;
            $detail->department = $request->department;
            $detail->save();
            return redirect()->route('employee.index')->with('success', __('Employee successfully created.'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (\Auth::user()->type == 'company' || \Auth::user()->type == 'employee' && \Auth::user()->id == $id) {
            // $eId        =   \Crypt::decrypt($id);
            $user       =   User::find($eId);
            $userDetails  =   UserDetail::find($eId);
            $employee   =   Employee::where('user_id', $eId)->first();
            return view('employee.view', compact('user', 'employee', 'userDetails'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function permission_arr()
    {
        $arr=[

            1 =>'create user',
            2 =>'edit user',
            3 =>'delete user',
            4 =>'manage user',
            5 =>'manage crm',
            6 =>'manage support ticket'
        ];
        return $arr;
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::user()->can('create member') || Auth::user()->can('create employee') || Auth::user()->can('create user') || \Auth::user()->type == 'employee' && \Auth::user()->id == $id) {
            $permissions=$this->permission_arr();
            $user=User::with('userDetail')->find($id);
            $userDetails = $user->userDetail;
            // $eId        = \Crypt::decrypt($id);
        // //Branchges
        // $branches = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        // $branches->prepend('Select Branch', '');
        // //Department
        // $department = Department::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        // $department->prepend('Select Department', '');
        // //Designation
        // $designation = Designation::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        // $designation->prepend('Select Designation', '');
        // //SalaryType
        // $salaryType = SalaryType::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        // $salaryType->prepend('Select Type', '');
        // $user = User::find($eId);
        // $employee = Employee::where('user_id', $eId)->first();
        // $employeesId  = \Auth::user()->employeeIdFormat(!empty($employee->employee_id) ? $employee->employee_id : '');
        // $departmentData  = Department::where('created_by', \Auth::user()->creatorId())->where('branch_id', $employee->branch_id)->get()->pluck('name', 'id');
        // //  dd($departmentData);

        // return view('employee.edit', compact('user', 'departmentData', 'employeesId', 'branches', 'employee', 'department', 'designation', 'salaryType'));
            return view('employee.edit',compact('permissions','user','userDetails'));
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
        if (Auth::user()->can('edit member') || Auth::user()->can('edit employee') || Auth::user()->can('edit user') || \Auth::user()->type == 'employee' && \Auth::user()->id == $id) {
            $validator = Validator::make(
                $request->all(), [
                    'name' => 'required|max:120',
                    'email' => 'required|email|unique:users,email,'.$id,
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }
            // $permissions=$this->permission_arr();
            // $permission_arr=[];

            // foreach($permissions as $key => $permission)
            // {
            //    foreach ($request->permissions as $ke => $value) {
            //         if($key==$value)
            //         {
            //             $permission_arr[$key]=$permission;
            //         }
            //    }
            // }

            $user =User::where('id',$id)->first();
            $user['name'] = $request->name;
            $user['email'] = $request->email;
            // $user['permission_json'] = json_encode($permission_arr);
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
                    return redirect()->back()->with('error', __($path['msg']));
                }
                $user->avatar && Utility::delete_directory( $user->avatar);

                $user->avatar = $fileNameToStore;
            }
            $user->save();

            $detail = $user->userDetail;
            $detail->mobile_number = $request->mobile_number;
            $detail->land_phone = $request->land_phone;
            $detail->extension_number = $request->extension_number;
            $detail->profession = $request->profession;
            $detail->department = $request->department;
            $detail->save();

            return redirect()->route('employee.index')->with('success', __('Employee successfully Updated.'));
        }
        return redirect()->back()->with('error', __('Permission Denied.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $premission=[];
        if(\Auth::user()->super_admin_employee==1)
        {
            $premission=json_decode(\Auth::user()->permission_json);
            $premission_arr = get_object_vars($premission);
        }

        if ((Auth::user()->can('delete member') || Auth::user()->can('delete employee') || Auth::user()->can('delete user')) || (Auth::user()->super_admin_employee==1 )) {

            $user = User::find($id);
            $detail = UserDetail::where('user_id', $user->id)->first();


            if ($user->created_by != Auth::user()->creatorId() && Auth::user()->type!='company') {
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

                    return redirect()->back()->with('success', __('Employee deleted successfully.'));
                }
            }
        } else {
            return redirect()->back()->with('error', __('Employee not found.'));
        }
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

    public function employeeCompanyInfoEdit(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'branch_id'         => 'required',
                'department_id'     => 'required',
                'designation_id'    => 'required',
                'joining_date'      => 'required',
                'salary_type'       => 'required',
                'salary'            => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        if (!empty($request->name)) {
            $user       = User::find($id);
            $user->name = $request->name;

            $user->save();
        }

        $employee                    =      Employee::where('user_id', $id)->first();
        $employee->branch_id         =      $request->branch_id;
        $employee->department        =      $request->department_id;
        $employee->designation       =      $request->designation_id;
        $employee->joining_date      =      date("Y-m-d", strtotime($request->joining_date));
        $employee->exit_date         =      !empty($request->exit_date) ? date("Y-m-d", strtotime($request->exit_date)) : new \DateTime();
        $employee->salary_type       =      $request->salary_type;
        $employee->salary            =      $request->salary;
        $employee->save();

        return redirect()->back()->with(
            'success',
            'Employee company successfully updated.'
        );
    }
}
