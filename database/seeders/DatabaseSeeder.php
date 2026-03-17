<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Supervisor;
use App\Models\Employee;
use App\Models\PhoneLine;
use App\Models\CallRecord;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'role' => 'admin',
                'password' => bcrypt('password123'),
            ]
        );

        // Dummy supervisor user
        $supervisorUser = User::updateOrCreate(
            ['username' => 'supervisor1'],
            [
                'name' => 'Supervisor 1',
                'email' => 'supervisor1@example.com',
                'role' => 'supervisor',
                'password' => bcrypt('password123'),
            ]
        );

        $supervisor = Supervisor::updateOrCreate(
            ['user_id' => $supervisorUser->id],
            ['extension' => '2001']
        );

        // Dummy employees
        $employees = collect([
            ['name' => 'Ali', 'extension' => '3001', 'department' => 'Sales', 'status' => 'online'],
            ['name' => 'Sara', 'extension' => '3002', 'department' => 'Support', 'status' => 'on-call'],
            ['name' => 'Mahmoud', 'extension' => '3003', 'department' => 'Sales', 'status' => 'idle'],
            ['name' => 'Noor', 'extension' => '3004', 'department' => 'Support', 'status' => 'offline'],
        ])->map(function ($data) use ($supervisor) {
            return Employee::updateOrCreate(
                ['extension' => $data['extension']],
                [
                    'name' => $data['name'],
                    'department' => $data['department'],
                    'supervisor_id' => $supervisor->id,
                    'status' => $data['status'],
                    'idle_since' => null,
                ]
            );
        });

        // Dummy phone lines
        $salesLine = PhoneLine::updateOrCreate(
            ['number' => '1001'],
            ['name' => 'Sales Line', 'type' => 'incoming', 'status' => 'active']
        );

        $supportLine = PhoneLine::updateOrCreate(
            ['number' => '1002'],
            ['name' => 'Support Line', 'type' => 'both', 'status' => 'active']
        );

        // Attach employees to lines
        foreach ($employees as $employee) {
            $employee->phoneLines()->syncWithoutDetaching(
                $employee->department === 'Sales' ? [$salesLine->id] : [$supportLine->id]
            );
        }

        // Dummy call records (last few hours)
        $now = now();
        foreach ($employees as $index => $employee) {
            for ($i = 0; $i < 5; $i++) {
                $started = $now->copy()->subMinutes(rand(10, 240));
                $duration = rand(30, 600);
                $ended = $started->copy()->addSeconds($duration);

                CallRecord::create([
                    'employee_id' => $employee->id,
                    'phone_line_id' => $employee->department === 'Sales' ? $salesLine->id : $supportLine->id,
                    'direction' => $i % 2 === 0 ? 'incoming' : 'outgoing',
                    'status' => $i % 4 === 0 ? 'missed' : 'answered',
                    'started_at' => $started,
                    'ended_at' => $ended,
                    'duration_seconds' => $duration,
                    'caller' => '+2010000000' . $i,
                    'callee' => $employee->extension,
                    'raw_cdr_id' => 'CDR-' . $employee->id . '-' . $i,
                ]);
            }
        }
    }
}
