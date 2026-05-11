<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Photos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session()->has('success'))
            <div class="alert alert-success ">{{ session('success') }}</div>
            @endif
            @if(session()->has('danger'))
            <div class="alert alert-success ">{{ session('danger') }}</div>
            @endif

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                ID
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Photo name
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Image
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Options
                            </th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($photos as $photo )
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <td class="px-6 py-4">
                               {{$photo->id}}
                            </td>
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{$photo->name}}
                            </th>

                            <td class="px-6 py-4">
                                <img src="{{ asset('storage/'.$photo->image)}}" width=100 height=100>
                            </td>
                         
                            <td class="px-6 py-4 d-flex">
                                <a href="{{route('photos.edit',$photo->id)}}" class="font-medium text-green-600 dark:text-green-500 hover:underline">Edit</a>
                                <form action="{{route('photos.destroy',$photo->id)}}" method="post">
                                  @csrf
                                  @method('delete')
                                 <button type="submit" class="font-medium text-red-600 dark:text-red-500 hover:underline px-2">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach



                    </tbody>
                </table>
                {{ $photos->withQueryString()->links() }}
            </div>

        </div>
    </div>
</x-app-layout>