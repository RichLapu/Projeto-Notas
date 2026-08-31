<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::insert([
            ['name' => 'Desenvolvimento', 'color' => 'primary'],
            ['name' => 'Servidores / Infra', 'color' => 'danger'],
            ['name' => 'Universidade', 'color' => 'success'],
            ['name' => 'Pessoal', 'color' => 'warning'],
        ]);
    }
}