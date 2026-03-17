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

        // Dummy supervisors and employees
        $supervisors = collect([
            [
                'user' => ['username' => 'supervisor1', 'name' => 'Supervisor 1', 'email' => 'supervisor1@example.com'],
                'extension' => '2001',
                'employees' => [
                    ['name' => 'Ali', 'extension' => '3001', 'department' => 'Sales', 'status' => 'online'],
                    ['name' => 'Mahmoud', 'extension' => '3003', 'department' => 'Sales', 'status' => 'idle'],
                    ['name' => 'Omar', 'extension' => '3005', 'department' => 'Sales', 'status' => 'on-call'],
                ],
            ],
            [
                'user' => ['username' => 'supervisor2', 'name' => 'Supervisor 2', 'email' => 'supervisor2@example.com'],
                'extension' => '2002',
                'employees' => [
                    ['name' => 'Sara', 'extension' => '3002', 'department' => 'Support', 'status' => 'on-call'],
                    ['name' => 'Noor', 'extension' => '3004', 'department' => 'Support', 'status' => 'offline'],
                    ['name' => 'Laila', 'extension' => '3006', 'department' => 'Support', 'status' => 'online'],
                ],
            ],
        ]);

        $allEmployees = collect();

        $supervisors->each(function ($sup) use (&$allEmployees) {
            $user = User::updateOrCreate(
                ['username' => $sup['user']['username']],
                [
                    'name' => $sup['user']['name'],
                    'email' => $sup['user']['email'],
                    'role' => 'supervisor',
                    'password' => bcrypt('password123'),
                ]
            );

            $supervisor = Supervisor::updateOrCreate(
                ['user_id' => $user->id],
                ['extension' => $sup['extension']]
            );

            $employees = collect($sup['employees'])->map(function ($data) use ($supervisor) {
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

            $allEmployees = $allEmployees->merge($employees);
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
        foreach ($allEmployees as $employee) {
            $employee->phoneLines()->syncWithoutDetaching(
                $employee->department === 'Sales' ? [$salesLine->id] : [$supportLine->id]
            );
        }

        // Dummy call records (last few days)
        $now = now();
        foreach ($allEmployees as $index => $employee) {
            for ($d = 0; $d < 3; $d++) {
                for ($i = 0; $i < 8; $i++) {
                    $started = $now->copy()->subDays($d)->setTime(rand(8, 20), rand(0, 59));
                    $duration = rand(30, 900);
                    $ended = $started->copy()->addSeconds($duration);

                    CallRecord::create([
                        'employee_id' => $employee->id,
                        'phone_line_id' => $employee->department === 'Sales' ? $salesLine->id : $supportLine->id,
                        'direction' => $i % 2 === 0 ? 'incoming' : 'outgoing',
                        'status' => $i % 5 === 0 ? 'missed' : 'answered',
                        'started_at' => $started,
                        'ended_at' => $ended,
                        'duration_seconds' => $duration,
                        'caller' => '+201000' . rand(1000, 9999),
                        'callee' => $employee->extension,
                        'raw_cdr_id' => 'CDR-' . $employee->id . '-' . $d . '-' . $i,
                    ]);
                }
            }
        }
    }
}
