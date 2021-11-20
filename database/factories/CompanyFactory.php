<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
    	return [
    	    'name'    => $this->faker->name(),
            'phone'   => Str::random(10),
            'email'   => $this->faker->unique()->email,
            'address' => $this->faker->sentence(),
            'debtor_limit' => 500
    	];
    }
}
