<?php
// app/Http/Middleware/CheckPackageLimits.php
namespace App\Http\Middleware;

use Closure;

class CheckPackageLimits
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');

        $limit = $user->currentPackageItemsLimit(); // from package.items
        $currentCount = \App\Models\Product::where('user_id', $user->id)->count();

        if ($currentCount >= $limit) {
            return redirect()->route('seller.plans')
                ->with('error', __('You have reached your plan limit (:limit items). Please upgrade.', ['limit' => $limit]));
        }

        return $next($request);
    }
}
