<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CustomerSupportStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerQueryRequest;
use App\Http\Resources\CustomerSupportResource;
use App\Models\CustomerQuery;
use App\Models\User;
use Illuminate\Http\Request;
use Session;
use Yajra\DataTables\DataTables;

class CustomerSupportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{
            if($request->ajax()){
                $queries = CustomerQuery::latest()->get();
                return DataTables::of($queries)
                    ->addIndexColumn()
                    ->editColumn('name', function ($row) {
                        return $row->user->name ?? 'N/A';
                    })
                    ->editColumn('email', function ($row) {
                        return $row->user->email ?? 'N/A';
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at ? $row->created_at->format('Y-m-d - H:i:s A') : 'N/A';
                    })
                    ->addColumn('actions', function ($row) {
                        return view('content.admin.customer-supports.actions', [
                            'query' => $row,
                        ])->render();
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
            }
            return view('content.admin.customer-supports.index');
        }catch(\Exception $e){
            return response()->json(['error' => 'An error occurred', 'details' => $e->getMessage()], 500);
        }
    }

    public function reply(Request $request, CustomerQuery $customerQuery)
    {
        $request->validate([
            'response' => 'required|string'
        ]);
        try{
            $customerQuery->update([
                'response' => $request->response,
                'status' => CustomerSupportStatus::RESOLVED->value
            ]);
            return response()->json([
                'message'=>"Query Resolved"
            ], 200);
        }catch(\Throwable $th){
            return response()->json([
                'message'=>$th->getMessage()
            ], 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerQueryRequest $request)
    {
        try {
            $data = $request->validated();
            $user = User::where('email', $data['email'])->first();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            $query = CustomerQuery::create([
                'user_id' => $user->id,
                'query' => $data['query'],
                'status' => CustomerSupportStatus::PENDING->value
            ]);
            return $this->respondWithMessageAndPayload(new CustomerSupportResource($query), 'Query created successfully.');
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred', 'details' => $e->getMessage()], 500);
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerQuery $customerSupport)
    {
        try{
            $customerSupport->delete();
            return response()->json([
                'message' => 'Query deleted successfully'
            ], 200);
        }catch(\Exception $ex){
            return response()->json([
                'message' => 'Something Went Wrong'
            ], 200);
        }
    }
}
