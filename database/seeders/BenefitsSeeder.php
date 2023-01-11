<?php

namespace Database\Seeders;

use App\Models\Benefit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BenefitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            "Fast-growing startup",
            "Macbooks provided",
            "Legal support",
            "No overtime",
            "Professional Courses (covering costs)",
            "Health Insurance",
            "Annual Bonuses",
            "Multinational team",
            "Paid sick leaves",
            "Sports (covering costs)",
            "Providing equipment for work from home",
            "Business trips abroad",
            "No bureaucracy",
            "Unlimited vacation",
            "Big stable company",
            "English Courses (covering costs)",
            "Regular salary growth",
            "English-speaking environment",
            "Relocation assistance",
            "Sign-In Bonus",
            "Ability to master and apply new technologies on the project",
            "Accounting support (covering costs)",
            "Flexible working hours",
            "Fresh fruit and other snacks onsite",
            "Long-lasting projects",
            "No outdated technologies on the project",
            "Paid overtime",
            "Paid public holidays",
            "Parental leave",
            "Psychotherapist coverage",
            "Regular team buildings",
            "Remote forever",
            "20 days paid vacation",
            "Full remote and possibility work from the office",
            "Covid-19 support",
            "Fitness Zone",
            "Hobby courses/classes coverage",
            "Parking compensation",
            "Pet-friendly",
            "Well-organised business processes",
            "Work-life balance",
            "Unicorn company",
            "Direct influence on the product roadmap",
            "Free lunch",
            "Massage in office",
            "Unlimited learn and development budget",
            "Paid coworking space for remote employees",
            "Private workplace",
            "Dishes made by our Chef",
            "Stock Options",
            "Bar in the office",
            "Above market compensation rates",
            "Holidays with a team abroad",
            "Paid relocation",
            "Employee share plan"
        ];

        $insert = [];
        foreach ($data as $benefit) {
            $insert[] = [
                'name' => $benefit
            ];
        }

        Benefit::insert($insert);
    }
}
