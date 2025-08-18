<?php
namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SubscriptionController extends Controller
{
    public function plans()
    {
        // Show all packages with pricing per cycle
        $packages = Package::orderBy('items')->get();
        return view('seller.plans', compact('packages'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'package_id'   => 'required|exists:packages,id',
            'billing_cycle'=> 'required|in:monthly,quarterly,bi_annual,annual',
        ]);

        $package = Package::findOrFail($request->package_id);
        [$amount, $currency] = $this->computePrice($package, $request->billing_cycle);

        // Render a payment page (show amount). On success, call confirm()
        return view('seller.subscription_checkout', [
            'package'       => $package,
            'billing_cycle' => $request->billing_cycle,
            'amount'        => $amount,
            'currency'      => $currency,
        ]);
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'package_id'   => 'required|exists:packages,id',
            'billing_cycle'=> 'required|in:monthly,quarterly,bi_annual,annual',
            'txn_ref'      => 'required|string|max:100',
            'method'       => 'required|string|max:50',
        ]);

        $user    = auth()->user();
        $package = Package::findOrFail($request->package_id);
        [$amount, $currency, $durationMonths] = $this->computePrice($package, $request->billing_cycle, true);

        // End any current active subscription
        $current = $user->currentSubscription()->first();
        if ($current) {
            $current->update([
                'status'     => 'expired',
                'expires_at' => now(),
            ]);
        }

        // Create the new subscription
        $starts = now();
        $expires = $starts->copy()->addMonths($durationMonths);

        UserSubscription::create([
            'user_id'       => $user->id,
            'package_id'    => $package->id,
            'billing_cycle' => $request->billing_cycle,
            'price'         => $amount,
            'currency'      => $currency,
            'status'        => 'active',
            'starts_at'     => $starts,
            'expires_at'    => $expires,
            'last_paid_at'  => $starts,
            'next_due_at'   => $expires,
            'payment_method'=> $request->method,
            'payment_txn_ref'=> $request->txn_ref,
        ]);

        return redirect()->route('seller.dashboard')->with('success', __('Subscription activated.'));
    }

    private function computePrice(Package $p, string $cycle, bool $withMonths=false): array
    {
        // Use your precomputed totals from the table
        $currency = 'KES';
        $map = [
            'monthly'   => [$p->base_monthly_price, 1],
            'quarterly' => [$p->quarterly_total, 3],
            'bi_annual' => [$p->bi_annual_total, 6],
            'annual'    => [$p->annual_total, 12],
        ];
        [$amount, $months] = $map[$cycle] ?? [0,1];

        return $withMonths ? [(float)$amount, $currency, $months] : [(float)$amount, $currency];
    }
}
