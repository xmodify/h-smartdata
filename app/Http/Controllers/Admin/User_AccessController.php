<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User_Access;

class User_AccessController extends Controller
{
    public function index()
    {
        $user_access = User_Access::all();
        return view('admin.user_access.index', compact('user_access'));
    }

    // public function create()
    // {
    //     return view('admin.users.create');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'ptname' => 'required',
            'role' => 'required',
        ]);

        User_Access::create([
            'username' => $request->username,
            'ptname' => $request->ptname,
            'role' => 'user',
            'del_product' => '',
            'h_rims' => '',
        ]);

        return redirect()->route('admin.user_access.index')->with('success', 'เพิ่มข้อมูลสำเร็จ');
    }
    public function show(User_Access $user)
    {
        //
    }

    public function edit(User_Access $user)
    {
        //     
    }

    public function update(Request $request, User_Access $user)
    {
        $validated = $request->validate([
        'username' => 'required',
        'ptname' => 'required',
        'role' => 'required',
        ]);

        $data = [
            'username' => $request->username,
            'ptname' => $request->ptname,
            'role' => $request->role,
            'del_product' => $request->has('del_product') ? 'Y' : 'N',
            'h_rims' => $request->has('h_rims') ? 'Y' : 'N',          
        ];

        $user->update($data);

        return redirect()->route('admin.user_access.index')->with('success', 'แก้ไขข้อมูลสำเร็จ');
    }

    public function destroy(User_Access $user)
    {
        $user->delete();
        return redirect()->route('admin.user_access.index')->with('success', 'ลบข้อมูลสำเร็จ');
    }
    
    
}
