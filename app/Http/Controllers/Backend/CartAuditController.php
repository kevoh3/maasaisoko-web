<?php


namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Cart;

class CartAuditController extends Controller
{
    public function index()
    {
        $carts = Cart::with('user')
            ->latest()
            ->paginate(20);

        return view('backend.carts.index', compact('carts'));
    }

    public function show(Cart $cart)
    {
        $cart->load(['user', 'items.product']);
        return view('backend.carts.show', compact('cart'));
    }
}
