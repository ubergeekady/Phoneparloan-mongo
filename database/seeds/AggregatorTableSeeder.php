<?php

use Illuminate\Database\Seeder;

class AggregatorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Add this lines
        \App\Models\Aggregators::query()->truncate();
        // truncate user table each time of seeders run

        $aggregatores = [[
            'name' => 'Loan Dost',
            'logo' => '',
            'status' => 0
        ],[ // create a new user
            'name' => 'Credit Vidya',
            'logo' => '',
            'status' => 0
        ],[ // create a new user
            'name' => 'Money View',
            'logo' => '',
            'status' => 0
        ],
            [
            'name' => 'SubhLoan',
            'logo' => '',
            'status' => 0
        ],
            [
            'name' => 'Cahse',
            'logo' => '',
            'status' => 0
        ]

        ];

        foreach ($aggregatores as $aggregatore) {
            \App\Models\Aggregators::create($aggregatore);
        }
    }
}
