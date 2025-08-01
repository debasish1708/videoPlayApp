<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBannerRequest;
use App\Http\Requests\UpdateBannerRequest;
use App\Models\Banner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{
            if($request->ajax()){
                $banners = Banner::latest()->get();
                return DataTables::of($banners)
                    ->addIndexColumn()
                    ->addColumn('actions', function ($row) {
                        return view('content.admin.banners.actions', [
                            'banner' => $row,
                        ])->render();
                    })
                    ->editColumn('title', function($row) {
                        return $row->title ?? 'N/A';
                    })
                    ->editColumn('description', function($row) {
                        return $row->description ?? 'N/A';
                    })
                    ->editColumn('image', function($row) {
                        return $row->image ? '<img src="' . $row->image . '" alt="Banner Image" style="width: 100px; height: auto;">' : 'No Image';
                    })
                    ->editColumn('created_at',function($row){
                        return Carbon::parse($row->created_at)->format('d M Y h:i A') ?? 'N/A';
                    })
                    ->editColumn('url', function($row) {
                        return '<a href="' . $row->url . '" target="_blank">Url</a>';
                    })
                    ->rawColumns(['actions', 'url', 'image'])
                    ->make(true);
            }
            return view('content.admin.banners.index');
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return view('content.admin.banners.create');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateBannerRequest $request)
    {
        try{
            $data = $request->validated();
            if(isset($data['post_image'])){
                $imagePath = $request->file('post_image')->store('banners', 'public');
                $data['post_image'] = basename($imagePath);
            }
            Banner::create([
                'title' => $data['title'] ?? null,
                'description' => $data['description'] ?? null,
                'image' => $data['post_image'],
                'url' => $data['url'],
            ]);

            return redirect()->route('banners.index')->with('success', 'Banner created successfully');
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Banner $banner)
    {
        return view('content.admin.banners.show', compact('banner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {
        try {
            return view('content.admin.banners.edit', compact('banner'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBannerRequest $request, Banner $banner)
    {
        try {
            $data = $request->validated();
            // Handle image upload
            if ($request->hasFile('post_image')) {
                $imagePath = $request->file('post_image')->store('banners', 'public');
                $data['post_image'] = basename($imagePath);
            } else {
                // Keep the existing image if no new image is uploaded
                $data['post_image'] = basename($banner->image);
            }
            $banner->update([
                'title' => $data['title'] ?? null,
                'description' => $data['description'] ?? null,
                'image' => $data['post_image'],
                'url' => $data['url'],
            ]);
            return redirect()->route('banners.index')->with('success', 'Banner updated successfully');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // public function publish(Request $request, string $id)
    // {
    //     // Logic to publish or unpublish the banner
    //     $banner = Banner::findOrFail($id);
    //     $banner->status = $request->input('status') ? 'published' : 'unpublished';
    //     $banner->save();

    //     return response()->json(['success' => true, 'message' => 'Banner status updated successfully']);
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        try{
            $banner->delete();
            return response()->json([
                'message' => 'Banner deleted successfully'
            ], 200);
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
