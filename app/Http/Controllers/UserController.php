<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{
            if($request->ajax()){
                return DataTables::of(User::query())
                    ->addIndexColumn()
                    ->addColumn('actions', function ($row) {
                        return view('content.users.actions', [
                            'user' => $row,
                        ])->render();
                    })
                    ->editColumn('created_at',function($row){
                        return Carbon::parse($row->start_date)->format('d M Y h:i A') ?? 'N/A';
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
            }
            return view('content.users.index');
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required','string','max:50'],
            'email' => ['required','email'],
            'password' => ['required','string','min:6']
        ]);
        try{
            User::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>bcrypt($request->password)
            ]);
            return redirect()->back()->with('success', 'User created successfully');
        }catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('content.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required','string','max:50'],
            'email' => ['required','email'],
            'password' => ['nullable','string','min:6']
        ]);

        try{
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->filled('password') ? bcrypt($request->password) : $user->password
            ]);
            return redirect()->back()->with('success', 'User updated successfully');
        }catch(\Exception $ex){
            return redirect()->back()->with('error', $ex->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try{
            $user->delete();
            return response()->json([
                'message' => 'User deleted successfully'
            ],200);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
