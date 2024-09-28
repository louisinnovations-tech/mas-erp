<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use App\Models\UserCatgory;
use Illuminate\Http\Request;
use App\Models\Item;

class CategoryController extends Controller
{

    public function index(Request $request)
    {
        $user = \Auth::user();
        if(\Auth::user()->can('manage crm'))
        {

            $categories = Category::with(['users'])->where('created_by',\Auth::user()->supportTicketCreatorId())->get();


            return view('category.index', compact('categories'));
        }
        else
        {
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
        $types = Category::$categoryType;

        return view('category.create', compact('types'));
        // $user = \Auth::user();
        // $users = User::where('created_by',\Auth::user()->supportTicketCreatorId())->get()->pluck('name','id');
        // $users->users  = explode(',', $user->users);
        // if(\Auth::user()->can('manage crm'))
        // {

        //     return view('category.create',compact('users'));

        // }
        // else
        // {
        //     return redirect()->back()->with('error', __('Permission denied.'));
        // }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(\Auth::user()->type == 'company')
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required',
                                   'type' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $category             = new Category();
            $category->name       = $request->name;
            $category->type       = $request->type;
            $category->created_by = \Auth::user()->creatorId();
            $category->save();

            return redirect()->route('category.index')->with('success', __('Category successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
        // $user = \Auth::user();
        // if(\Auth::user()->can('manage crm'))
        // {
        //     $validation = [
        //         'name' => [
        //             'required',
        //             'string',
        //             'max:255',
        //         ],
        //         'color' => [
        //             'required',
        //             'string',
        //             'max:255',
        //         ],
        //     ];
        //     $request->validate($validation);



        //     $category = new Category();
        //     $category->name = $request->name;
        //     $category->color = $request->color;
        //     $category->created_by = \Auth::user()->supportTicketCreatorId();
        //     $category->save();
        //     if(!empty($request->users)){
        //         foreach($request->users as $value)
        //         {
        //             $usercategory = UserCatgory::create([
        //             'user_id' => $value,
        //             'category_id' => $category->id,
        //             ]);
        //         }
        //     }


        //     return redirect()->route('category.index')->with('success', __('Category created successfully'));
        // }
        // else
        // {
        //     return redirect()->back()->with('error', __('Permission denied.'));
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        $types = Category::$categoryType;

        return view('category.edit', compact('category', 'types'));
        // $userObj = \Auth::user();
        // $category = Category::find($id);
        // $users = User::where('created_by',\Auth::user()->supportTicketCreatorId())->get()->pluck('name','id');
        // $catgoryuser = UserCatgory::where('category_id',$category->id)->get()->pluck('user_id');
        // $users->prepend(__('Select User'), '');
        // $users->users  = explode(',', $userObj->users);
        // $userObj->categories  = explode(',', $userObj->categories);

        // if(\Auth::user()->can('manage crm'))
        // {
        //     $category = Category::find($id);

        //     return view('category.edit', compact('category','users','catgoryuser'));
        // }
        // else
        // {
        //     return redirect()->back()->with('error', __('Permission denied.'));
        // }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Category $category)
    {

        if(\Auth::user()->type == 'company')
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:20',
                                   'type' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $category->name = $request->name;
            $category->type = $request->type;
            $category->save();

            return redirect()->route('category.index')->with('success', __('Category successfully updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
        // $userObj = \Auth::user();
        // if(\Auth::user()->can('manage crm'))
        // {
        //     $category        = Category::find($id);

        //     $category->name  = $request->name;
        //     $category->color = $request->color;
        //     if(!empty($request->users)){
        //         UserCatgory::where('category_id',$category->id)->delete();
        //         foreach($request->users as $value)
        //         {
        //             $usercategory = UserCatgory::create([
        //             'user_id' => $value,
        //             'category_id' => $category->id,
        //             ]);
        //         }
        //     }
        //     $category->save();

        //     return redirect()->route('category.index')->with('success', __('Category updated successfully'));
        // }
        // else
        // {
        //     return redirect()->back()->with('error', __('Permission denied.'));
        // }
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        if(\Auth::user()->type == 'company')
        {
            if($category->type == 0)
            {
                $data = Item::where('category', $category->id)->first();
            }
            elseif($category->type == 1)
            {
                $data = Estimate::where('category', $category->id)->first();
            }
            else
            {
                $data = Project::where('category', $category->id)->first();
            }

            if(!empty($data))
            {
                return redirect()->back()->with('error', __('this category is already use so please transfer or delete this category related data.'));
            }
            $category->delete();

            return redirect()->route('category.index')->with('success', __('Category successfully deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
