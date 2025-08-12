<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('groups')->insert([
            [
                'name' => 'Kasarani Traders Association',
                'registration_number' => 'REG-2023-001',
                'type' => 'association',
                'industry' => 'Retail',
                'contact_person' => 'Jane Wambui',
                'phone' => '0722000000',
                'email' => 'kasarani@example.com',
                'address' => 'Kasarani, Nairobi',
                'county' => 'Nairobi',
                'sub_county' => 'Kasarani',
                'kra_pin' => 'P051234567X',
                'business_permit_number' => 'BP-NAI-2023-1001',
                'certificate_of_incorporation' => 'uploads/groups/certs/kasarani_cert.pdf',
                'tax_compliance_certificate' => 'uploads/groups/tax/kasarani_tax.pdf',
                'bank_name' => 'Equity Bank',
                'bank_account_number' => '1234567890',
                'bank_branch' => 'Kasarani',
                'website' => 'http://kasaranitraders.org',
                'social_media' => '@kasaranitraders',
                'status' => 'active',
                'verified_by' => 1,
                'verified_at' => now()->subDays(2),
                'verified_status' => 'verified',
                'verified_notes' => 'All KYC documents verified.',
                'approved_by' => 1,
                'approved_at' => now()->subDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Clay City Artisans Co-op',
                'registration_number' => 'COOP-2024-045',
                'type' => 'cooperative',
                'industry' => 'Craft & Art',
                'contact_person' => 'John Mwangi',
                'phone' => '0733000000',
                'email' => 'claycity@example.com',
                'address' => 'Clay City, Nairobi',
                'county' => 'Nairobi',
                'sub_county' => 'Roysambu',
                'kra_pin' => 'P051234568Y',
                'business_permit_number' => 'BP-NAI-2024-2030',
                'certificate_of_incorporation' => 'uploads/groups/certs/claycity_cert.pdf',
                'tax_compliance_certificate' => 'uploads/groups/tax/claycity_tax.pdf',
                'bank_name' => 'Co-operative Bank',
                'bank_account_number' => '2233445566',
                'bank_branch' => 'Roysambu',
                'website' => 'http://claycitycoop.org',
                'social_media' => '@claycitycoop',
                'status' => 'pending',
                'verified_by' => null,
                'verified_at' => null,
                'verified_status' => 'pending',
                'verified_notes' => null,
                'approved_by' => null,
                'approved_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

    }
}
