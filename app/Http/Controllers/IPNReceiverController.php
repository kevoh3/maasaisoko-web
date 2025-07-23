<?php
namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IPNReceiverController extends Controller
{
public function handle(Request $request)
{
$data = $request->all();

// Log the data for debugging
Log::info('Received IPN from Propel:', $data);

// Example: store into a DB
\DB::table('received_ipns')->insert([
'data' => json_encode($data),
'received_at' => now(),
]);
    $accountNumber = $data['bill_ref_number'] ?? null;
    $amount = (float) $data['amount'] ?? 0;

    if (!$accountNumber || !$amount) {
        return response()->json(['status' => 'error', 'message' => 'Invalid IPN data'], 400);
    }
    // Find wallet using bill_ref_number
    $wallet = Wallet::where('account_number', $accountNumber)->first();

    if (!$wallet) {
        Log::warning("No wallet found for account number: $accountNumber");
        return response()->json(['status' => 'error', 'message' => 'Wallet not found'], 404);
    }
    // Update balance
    $wallet->balance += $amount;
    $wallet->save();
    Log::info("Updated wallet {$wallet->id} balance by {$amount}");

    Log::info("Updated wallet {$wallet->id} balance by {$amount}");

// Always respond with 200 OK
return response()->json(['status' => 'received'], 200);
}
}
