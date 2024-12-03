<?php

namespace Database\Seeders;

use App\Kernel\Filters\TopScoreFilter;
use App\Models\Filter;
use Illuminate\Database\Seeder;

class FiltersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Filter::query()->updateOrInsert(['id' => 1], [
            'id' => 1,
            'title' => 'С наивысшим количеством PP',
            'model' => TopScoreFilter::class,
        ]);
    }
}
