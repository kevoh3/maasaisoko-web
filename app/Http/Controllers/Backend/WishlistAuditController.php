<?php


namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;

class WishlistAuditController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::with('user')
            ->latest()
            ->paginate(20);

        return view('backend.wishlists.index', compact('wishlists'));
    }

    public function show(Wishlist $wishlist)
    {
        $wishlist->load(['user', 'items.product']);
        return view('backend.wishlists.show', compact('wishlist'));
    }
}
