<?php

namespace Database\Seeders;

use App\Models\Domain;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $domains = [
            'AI',
            'Accounting',
            'Advertising',
            'Agriculture',
            'Alcohol & Tobacco',
            'Analytics',
            'Applied Cryptography',
            'Automotive',
            'Autonomous Driving',
            'Acviation',
            'Banking',
            'Blockchain',
            'Business Intelligence',
            'Chatbots',
            'Construction',
            'Consulting',
            'Crypto',
            'Data communication',
            'Dating',
            'Delivery',
            'E-learning',
            'E-commerce',
            'Ecology',
            'Education',
            'Electronic document circulation',
            'Energy',
            'Entertainment',
            'Finance',
            'Fintech',
            'Gambling & iGaming',
            'GameDev',
            'HR & Recruiting',
            'Healthcare',
            'Industrial automation',
            'Information Technology',
            'Insurance',
            'IoT',
            'Legal',
            'Logistics & Distribution',
            'Machine Learning',
            'Manufactoring',
            'Marketing',
            'Marketing & PR',
            'Marketplace',
            'Media',
            'Mining',
            'Productivity tools',
            'Real Estate',
            'Retail',
            'Saas',
            'Security',
            'Smart Industry',
            'Social Network',
            'Sports',
            'Statistics',
            'Telecom',
            'Tourism',
            'Transport',
            'Travel'
        ];
        $insert = [];
        foreach ($domains as $domain) {
            $insert[] = [
                'name' => $domain
            ];
        }

        Domain::insert($insert);
    }
}
