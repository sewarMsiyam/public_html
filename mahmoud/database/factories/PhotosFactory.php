<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Photos>
 */
class PhotosFactory extends Factory
{

    public function definition(): array
    {
        $name=$this->faker->words(2,true);

        $filePath = storage_path('app/public/photos');



        return [
           'name'=>$name,
            'slug'=> Str::slug($name),
            'image' =>  $this->faker->imageUrl(800,600), 
          
        ];
    }
}
