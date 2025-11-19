<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // create default users

        DB::table('users')->insert([
            'username' => 'superadmin',
            'password' => bcrypt('123456'),
            'role' => 'superadmin',
            'active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('users')->insert([
            'username' => 'admin',
            'password' => bcrypt('123456'),
            'role' => 'admin',
            'active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        DB::table('users')->insert([
            'username' => 'cashier',
            'password' => bcrypt('123456'),
            'role' => 'cashier',
            'active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // insert dummy product category
        $dummyProducts = [
            'Rice' => [
                [
                    'name' => 'Pork Rice',
                    'unit_price' => 2
                ],
                [
                    'name' => 'Chicken Rice',
                    'unit_price' => 2
                ],
                [
                    'name' => 'Fried Rice',
                    'unit_price' => 2
                ]
            ],
            'Noodle' => [
                [
                    'name' => 'Beef Noodle Soup',
                    'unit_price' => 3
                ],
                [
                    'name' => 'Seafood Noodle Soup',
                    'unit_price' => 3
                ]
            ],
            'Snack' => [
                [
                    'name' => 'Fried Chicken',
                    'unit_price' => 1
                ],
                [
                    'name' => 'French Fries',
                    'unit_price' => 1
                ],
                [
                    'name' => 'Burger',
                    'unit_price' => 1
                ]
            ],
            'Water' => [
                [
                    'name' => 'Eau Kulen',
                    'unit_price' => 0.25
                ],
                [
                    'name' => 'Angkor Puro',
                    'unit_price' => 0.25
                ],
                [
                    'name' => 'Dasani',
                    'unit_price' => 0.25
                ]
            ],
            'Coffee' => [
                [
                    'name' => 'Iced Latte',
                    'unit_price' => 1.5
                ],
                [
                    'name' => 'Iced Cappuccino',
                    'unit_price' => 1.5
                ],
                [
                    'name' => 'Iced Americano',
                    'unit_price' => 1.5
                ]
            ],
            'Beer & Wine' => [
                [
                    'name' => 'ABC',
                    'unit_price' => 2
                ],
                [
                    'name' => 'Tiger',
                    'unit_price' => 2
                ],
                [
                    'name' => 'Heineken',
                    'unit_price' => 2
                ]
            ],
            'Juice' => [
                [
                    'name' => 'Orange Juice',
                    'unit_price' => 2
                ],
                [
                    'name' => 'Apple Juice',
                    'unit_price' => 2
                ],
                [
                    'name' => 'Mango Juice',
                    'unit_price' => 2
                ]
            ],
        ];

        foreach ($dummyProducts as $key => $value) {
            $categoryId = DB::table('product_categories')->insertGetId([
                'name' => $key,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            foreach ($value as $product) {
                DB::table('products')->insert([
                    'name' => $product['name'],
                    'unit_price' => $product['unit_price'],
                    'product_category_id' => $categoryId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        // Dummy Table
        for ($i = 1; $i <= 10; $i++) {
            DB::table('tables')->insert([
                'name' => str_pad($i, 3, '0', STR_PAD_LEFT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
