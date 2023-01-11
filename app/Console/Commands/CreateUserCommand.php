<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $profile = [
            'first_name' => 'User',
            'last_name' => 'Simple',
            'country_code' => '+371',
            'phone' => '1234567890',
            'birthdate' => '1992-01-01',
            'country_id' => '121',
            'city_id' => '64706',
            'education' => 'top',
            'telegram' => 'test_telega',
            'whatsapp' => '1234567890',
            'job_role' => '',
            'is_journey_finished' => '1',
            'native_language_id' => '134',
        ];

        $userData = [
            'type' => 'user',
            'email' => 'customer@email.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('q1w2e3r4t5')
        ];

        $user = User::create($userData);
        $user->profile()->create($profile);

        $profile = [
            'first_name' => 'Company',
            'last_name' => 'Test',
            'country_code' => '+371',
            'phone' => '4352345676',
            'birthdate' => '1992-01-01',
            'country_id' => '121',
            'city_id' => '64706',
            'education' => 'top',
            'telegram' => 'test_telega',
            'whatsapp' => '1234567890',
            'job_role' => 'HR',
            'is_journey_finished' => '1',
            'native_language_id' => '134',
        ];

        $userData = [
            'type' => 'company',
            'email' => 'company@email.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('q1w2e3r4t5')
        ];

        $companyData = [
            'name' => 'TopCompany',
            'type' => '',
            'website' => 'https://company.com',
            'number_of_employees' => '100',
            'country_id' => '121',
            'city_id' => '64706',
        ];

        $user = User::create($userData);
        $user->profile()->create($profile);
        $user->companies()->create($companyData);

        return 0;

    }
}
