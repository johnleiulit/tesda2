<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Application;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('en_PH'); // Philippine locale

        // NC Programs available
        $ncPrograms = [
            'BOOKKEEPING NC III',
            'TOURISM PROMOTION SERVICES NC II',
            'EVENTS MANAGEMENT SERVICES NC III',
            'PHARMACY SERVICES NC III',
            'VISUAL GRAPHIC DESIGN NC III',
        ];

        // Create 20 test applications
        for ($i = 1; $i <= 20; $i++) {
            // Create applicant user
            $user = User::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'role' => 'applicant',
                'email_verified_at' => now(),
            ]);

            // Random status
            $statuses = ['pending', 'approved', 'rejected'];
            $status = 'pending';

            // Create application
            Application::create([
                'user_id' => $user->id,
                'title_of_assessment_applied_for' => $faker->randomElement($ncPrograms),
                'photo' => null,
                'surname' => $faker->lastName(),
                'firstname' => $faker->firstName(),
                'middlename' => $faker->lastName(),
                'middleinitial' => strtoupper($faker->randomLetter()),
                'name_extension' => $faker->optional(0.2)->randomElement(['Jr.', 'Sr.', 'III', 'IV']),
                
                // Address
                'region_code' => '13',
                'region_name' => 'National Capital Region (NCR)',
                'province_code' => '1339',
                'province_name' => 'Metro Manila',
                'city_code' => '133904',
                'city_name' => $faker->randomElement(['Manila', 'Quezon City', 'Makati', 'Pasig', 'Taguig']),
                'barangay_code' => '133904001',
                'barangay_name' => $faker->streetName(),
                'district' => $faker->randomElement(['District 1', 'District 2', 'District 3']),
                'street_address' => $faker->streetAddress(),
                'zip_code' => $faker->postcode(),
                
                // Personal Info
                'mothers_name' => $faker->name('female'),
                'fathers_name' => $faker->name('male'),
                'sex' => $faker->randomElement(['Male', 'Female']),
                'civil_status' => $faker->randomElement(['Single', 'Married', 'Widowed', 'Separated']),
                'mobile' => '09' . $faker->numerify('#########'),
                'email' => $user->email,
                'highest_educational_attainment' => $faker->randomElement([
                    'High School Graduate',
                    'College Level',
                    'College Graduate',
                    'Vocational Graduate'
                ]),
                'employment_status' => $faker->randomElement(['Employed', 'Unemployed', 'Self-employed']),
                'birthdate' => $faker->date('Y-m-d', '-18 years'),
                'birthplace' => $faker->city(),
                'age' => $faker->numberBetween(18, 50),
                
                // Status
                // 'status' => $status,
                // 'training_status' => $status === 'approved' ? 'enrolled' : null,
                
                // Additional fields
                'nationality' => 'Filipino',
                'employment_before_training_status' => $faker->randomElement(['Employed', 'Unemployed']),
                'employment_before_training_type' => $faker->optional()->randomElement(['Wage Employed', 'Self-employed']),
                'birthplace_city' => $faker->city(),
                'birthplace_province' => $faker->state(),
                'birthplace_region' => 'Region III',
                'educational_attainment_before_training' => $faker->randomElement([
                    'High School Graduate',
                    'College Level',
                    'College Graduate'
                ]),
                'parent_guardian_name' => $faker->name(),
                'parent_guardian_street' => $faker->streetAddress(),
                'parent_guardian_barangay' => $faker->streetName(),
                'parent_guardian_city' => $faker->city(),
                'parent_guardian_province' => $faker->state(),
                'parent_guardian_region' => 'Region III',
                'learner_classification' => json_encode(['Student']),
                'scholarship_type' => $faker->optional()->randomElement(['TWSP', 'PESFA', 'Private']),
                'privacy_consent' => true,
                
                'created_at' => $faker->dateTimeBetween('-30 days', 'now'),
            ]);
        }

        $this->command->info('✅ Created 20 test applications with users');
    }
}
