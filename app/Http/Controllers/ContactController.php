<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::find(Auth::id());
        $data = $user->contacts;
        return view('home', compact('data'));
    }

    public function search(Request $request)
    {
        $name = $request->input('name');
        $phone = $request->input('phone');
        
        $query = Contact::where('user_id', auth()->id());

        if ($name) { 
            $query->where('name', 'like', '%' . $name . '%');
        }

        if ($phone) {
            $query->where('phone', 'like', '%' . $phone . '%');
        }
        $data = $query->get();

        return view('home', compact('data'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => [
                    'required',
                    'numeric',
                    'regex:/^[0-9]{11,12}$/',
                ],
            ], [
                'phone.regex' => 'Nomor telepon harus berupa angka dan terdiri dari 11 hingga 12 digit.',
            ]);


            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Failed to create user');
            }

            $user = new Contact();
            $user->name = $request->input('name');
            $user->phone = $request->input('phone');
            $user->user_id = Auth::id();
            $user->save();

            return redirect()->route('users.index')->with('success', 'User added successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to create user');
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Contact::find($id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'string|max:255',
                'phone' => [
                    'numeric',
                    'regex:/^[0-9]{11,12}$/',
                ],
            ], [
                'phone.regex' => 'Nomor telepon harus berupa angka dan terdiri dari 11 hingga 12 digit.',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422); // Mengembalikan respon JSON dengan kode status unprocessable entity
            }

            $user->name = $request->input('name');
            $user->phone = $request->input('phone');
            $user->save();

            return response()->json(['success' => true, 'message' => 'User updated successfully']);

        } catch (\Throwable $th) {
            return response()->json(['error' => 'Failed to update user'], 500); // Mengembalikan respon JSON dengan kode status server error
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $user = Contact::destroy($id);
            if (!$user) {
                return redirect()->back()->with('error', 'User not found');
            }
            return redirect()->route('users.index')->with('success', 'User deleted successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to delete user');
        }
    }
}
