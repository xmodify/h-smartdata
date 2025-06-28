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
        ]);

        return redirect()->route('admin.user_access.index')->with('success', 'เพิ่มข้อมูลสำเร็จ');
    }

    // public function edit(User $user)
    // {
    //     return view('admin.users.edit', compact('user'));
    // }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
        'username' => 'required',
        'ptnaame' => 'required',
        'role' => 'required',
        ]);

        $data = [
            'username' => $request->username,
            'ptname' => $request->ptname,
            'role' => $request->role,
            'del_product' => $request->has('active') ? 'Y' : 'N',           
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
