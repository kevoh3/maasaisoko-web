<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Package;
use App\Models\UserSubscription;
use Carbon\Carbon;

class AssignFreeTierSeeder extends Seeder
{
    public function run(): void
    {
        $freePackage = Package::where('name', 'Free')->first();

        if (!$freePackage) {
            $this->command->error("Free package not found. Run FreePackageSeeder first.");
            return;
        }

        User::where('role_id', 3) // ✅ only sellers
        ->chunk(100, function ($users) use ($freePackage) {
            foreach ($users as $user) {
                if (!$user->subscriptions()->exists()) {
                    UserSubscription::create([
                        'user_id'     => $user->id,
                        'package_id'  => $freePackage->id,
                        'billing_cycle' => 'monthly',
                        'price'       => 0,
                        'currency'    => 'KES',
                        'status'      => 'active',
                        'starts_at'   => Carbon::now(),
                        'expires_at'  => Carbon::now()->addMonth(),
                    ]);
                }
            }
        });
    }
}
