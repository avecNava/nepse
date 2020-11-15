<?php

namespace Database\Factories;

use App\Models\Shareholder;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShareholderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Shareholder::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {   
        //https://github.com/fzaninotto/Faker        
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->freeEmail,
            //date($format = 'Y-m-d', $max = 'now') // '1979-06-09'
            'date_of_birth' => $this->faker->date,
            'relation' => $this->faker->word,
            'user_id' => $this->faker->randomDigitNot(0),
            'parent_id' => $this->faker->numberBetween(1,5)
        ];
    }
}
