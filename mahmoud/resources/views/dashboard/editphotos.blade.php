<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Photos') }}
        </h2>
    </x-slot>

    <div class="py-12">
       
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">




            <form class="max-w-sm mx-auto" method="post" action="{{route('photos.update',$photo->id)}}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-5">
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name Photo</label>
                    <input type="text" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 
                    text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 
                    dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500
                     dark:focus:border-blue-500" 
                     name="name" placeholder="Name Of Photo " required value="{{$photo->name}}" />
                </div>
                <div class="mb-5">
                    <label for="image" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">uplad photo </label>
                    <input type="file" id="image" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm 
                    rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 
                    dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 
                    dark:focus:border-blue-500" name='image'  required  
                     onchange="document.getElementById('preview').src = window.URL.createObjectURL(this.files[0])" />
                    
                </div>
                <div class="mb-5">
                     <img id="preview" src="{{ asset('storage/'.$photo->image)}}" width=100 height=100>
                </div>

               
               
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none
                 focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center 
                 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit Photo </button>
            </form>


        </div>
    </div>
</x-app-layout>