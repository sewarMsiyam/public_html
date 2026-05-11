<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Photos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $photos=Photos::orderBy('id','desc')->paginate();

      
        return view('dashboard.photos',[
            'photos'=>$photos
        ]);
    }

    public function create()
    {
        return view('dashboard.addphotos');
    }

   
    public function store(Request $request)
    {
        $request->validate([
            'image'=>"required",
            'name'=>"required"
        ]);
          $request->merge([
            'slug' => Str::slug($request->post('name')),
        ]);

      
        $data = $request->except('image');
        $data['image'] = $this->uploadImage($request);
        $product = Photos::create($data);

        return redirect(route('photos.index'))
            ->with('success', 'Photos Created Successfully');

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
        $photo=Photos::findOrFail($id);
        return view('dashboard.editphotos',[
            'photo'=>$photo
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $photo=Photos::findOrFail($id);
        $request->validate([
            'image'=>"required",
            'name'=>"required",
        ]);
        $old_img = $photo->image;
      
        $data = $request->except('image');
        $new_image = $this->uploadImage($request);
        if ($new_image) {
           
            $data['image'] = $new_image;
        }
        $photo->update($data);

        if ($old_img && isset($new_image)) {
            Storage::disk('public')->delete($old_img);
        }

        return redirect(route('photos.index'))->with('success', 'Photo Update Succesfully');



    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $photo=Photos::findOrFail($id);
        $old_img=$photo->image;
        if ($old_img ) {
            Storage::disk('public')->delete($old_img);
        }

        $photo->delete($id);
        return redirect(route('photos.index'))->with('danger', 'Photo Deleted');
    }

    protected function uploadImage(Request $request)
    {

        if (!$request->hasFile('image')) {
            return;
        }

        $file = $request->file('image'); // Uploaded File Object
        $path =  $file->store(
            'photos',
            [
                'disk' => 'public' // حددنا  الديسك  عبرنامع ستوريج بابليك
            ]
        );
        return  $path;
    }


}
