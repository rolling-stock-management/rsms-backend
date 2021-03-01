<?php

namespace Database\Seeders;

use App\Models\PassengerWagonType;
use Illuminate\Database\Seeder;

class PassengerWagonTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data() as $data) {
            PassengerWagonType::factory()->create([
                'name' => $data['name'],
                'description' => $data['description'],
                'interior_type_id' => $data['interior_type_id'],
                'repair_valid_for' => $data['repair_valid_for']
            ]);
        }
    }

    /**
     * Data to be seeded.
     *
     * @return string[][]
     */
    private function data()
    {
        return [
            ['name' => '10-50', 'description' => null, 'interior_type_id' => 3, 'repair_valid_for' => 4],
            ['name' => '15-63', 'description' => null, 'interior_type_id' => 1, 'repair_valid_for' => 6],
            ['name' => '19-40', 'description' => null, 'interior_type_id' => 3, 'repair_valid_for' => 2],
            ['name' => '19-74', 'description' => null, 'interior_type_id' => 3, 'repair_valid_for' => 4],
            ['name' => '20-17', 'description' => null, 'interior_type_id' => 1, 'repair_valid_for' => 2],
            ['name' => '20-40', 'description' => null, 'interior_type_id' => 3, 'repair_valid_for' => 2],
            ['name' => '20-44', 'description' => null, 'interior_type_id' => 1, 'repair_valid_for' => 5],
            ['name' => '20-47', 'description' => null, 'interior_type_id' => 3, 'repair_valid_for' => 2],
            ['name' => '21-33', 'description' => null, 'interior_type_id' => 1, 'repair_valid_for' => 5],
            ['name' => '21-43', 'description' => null, 'interior_type_id' => 1, 'repair_valid_for' => 5],
            ['name' => '21-45', 'description' => null, 'interior_type_id' => 1, 'repair_valid_for' => 5],
            ['name' => '21-50', 'description' => null, 'interior_type_id' => 3, 'repair_valid_for' => 4],
            ['name' => '22-97', 'description' => null, 'interior_type_id' => 5, 'repair_valid_for' => 5],
            ['name' => '25-63', 'description' => null, 'interior_type_id' => 1, 'repair_valid_for' => 6],
            ['name' => '27-47', 'description' => null, 'interior_type_id' => 1, 'repair_valid_for' => 2],
            ['name' => '29-74', 'description' => null, 'interior_type_id' => 3, 'repair_valid_for' => 4],
            ['name' => '31-43', 'description' => null, 'interior_type_id' => 1, 'repair_valid_for' => 5],
            ['name' => '50-80', 'description' => null, 'interior_type_id' => 4, 'repair_valid_for' => 1],
            ['name' => '70-71', 'description' => null, 'interior_type_id' => 6, 'repair_valid_for' => 1],
            ['name' => '84-44', 'description' => null, 'interior_type_id' => 1, 'repair_valid_for' => 6],
            ['name' => '85-97', 'description' => null, 'interior_type_id' => 2, 'repair_valid_for' => 2],
        ];
    }
}
