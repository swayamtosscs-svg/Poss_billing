<?php

namespace Database\Seeders;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupportTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()->pluck('id')->all();
        if (empty($users)) {
            $users = User::factory()->count(3)->create()->pluck('id')->all();
        }
        SupportTicket::factory()->count(10)->state(function () use ($users) {
            return ['user_id' => fake()->randomElement($users)];
        })->create();
    }
}
