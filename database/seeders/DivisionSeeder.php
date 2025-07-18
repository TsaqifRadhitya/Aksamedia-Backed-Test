<?php

namespace Database\Seeders;

use App\Models\division;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dummys = ['Mobile Apps', 'QA', 'Full Stack', 'Backend', 'Frontend', 'UI/UX Designer'];
        foreach ($dummys as $dummy) {
            division::create([
                'name' => $dummy
            ]);
        }
    }
}
