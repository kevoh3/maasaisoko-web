<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\Product;
use App\Models\User;

// DB-backed
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Wishlist;
use App\Models\WishlistItem;

class CartController extends Controller
{
    /* =========================
     * Helpers
     * ========================= */
    private function actor(): array
    {
        return [Auth::id(), session()->getId()];
    }

    private function ensureCart(): Cart
    {
        [$userId, $sessionId] = $this->actor();

        $query = Cart::query()->where('status', 'open');
        if ($userId) {
            $query->where(function ($q) use ($userId, $sessionId) {
                $q->where('user_id', $userId)->orWhere('session_id', $sessionId);
            });
        } else {
            $query->where('session_id', $sessionId);
        }

        $cart = $query->first();
        if (!$cart) {
            $cart = Cart::create([
                'user_id'    => $userId,
                'session_id' => $sessionId,
                'status'     => 'open',
                'currency'   => 'KES',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } else {
            // keep both pointers fresh
            $dirty = false;
            if ($userId && !$cart->user_id) { $cart->user_id = $userId; $dirty = true; }
            if (!$cart->session_id) { $cart->session_id = $sessionId; $dirty = true; }
            if ($dirty) { $cart->save(); }
        }
        return $cart;
    }

    private function ensureWishlist(): Wishlist
    {
        [$userId, $sessionId] = $this->actor();

        $query = Wishlist::query();
        if ($userId) {
            $query->where(function ($q) use ($userId, $sessionId) {
                $q->where('user_id', $userId)->orWhere('session_id', $sessionId);
            });
        } else {
            $query->where('session_id', $sessionId);
        }

        $wl = $query->first();
        if (!$wl) {
            $wl = Wishlist::create([
                'user_id'    => $userId,
                'session_id' => $sessionId,
            ]);
        } else {
            $dirty = false;
            if ($userId && !$wl->user_id) { $wl->user_id = $userId; $dirty = true; }
            if (!$wl->session_id) { $wl->session_id = $sessionId; $dirty = true; }
            if ($dirty) { $wl->save(); }
        }
        return $wl;
    }

    private function snapshot(Product $p, ?User $seller): array
    {
        return [
            'name'      => $p->title,
            'thumbnail' => $p->f_thumbnail,
            'unit'      => $p->variation_size,
            'weight'    => 0,
            'seller'    => [
                'id'          => $seller->id ?? null,
                'name'        => $seller->name ?? '',
                'store_name'  => $seller->shop_name ?? '',
                'store_logo'  => $seller->photo ?? '',
                'store_url'   => $seller->shop_url ?? '',
                'email'       => $seller->email ?? '',
                'phone'       => $seller->phone ?? '',
                'address'     => $seller->address ?? '',
            ],
        ];
    }

    private function metaToArray($meta): array
    {
        if (is_array($meta)) return $meta;
        $arr = json_decode($meta ?? '[]', true);
        return is_array($arr) ? $arr : [];
    }

    private function syncSessionFromDBCart(Cart $cart): void
    {
        $items = CartItem::where('cart_id', $cart->id)->get();
        $sessionCart = [];

        foreach ($items as $ci) {
            $meta  = $this->metaToArray($ci->meta);
            $pid   = $ci->product_id;

            $sessionCart[$pid] = [
                'id'           => $pid,
                'name'         => $meta['name']      ?? '',
                'qty'          => (int) $ci->quantity,
                'price'        => (float) $ci->unit_price,
                'weight'       => $meta['weight']    ?? 0,
                'thumbnail'    => $meta['thumbnail'] ?? '',
                'unit'         => $meta['unit']      ?? '',
                'seller_id'    => $meta['seller']['id'] ?? null,
                'seller_name'  => $meta['seller']['name'] ?? '',
                'store_name'   => $meta['seller']['store_name'] ?? '',
                'store_logo'   => $meta['seller']['store_logo'] ?? '',
                'store_url'    => $meta['seller']['store_url'] ?? '',
                'seller_email' => $meta['seller']['email'] ?? '',
                'seller_phone' => $meta['seller']['phone'] ?? '',
                'seller_address'=> $meta['seller']['address'] ?? '',
            ];
        }
        session()->put('shopping_cart', $sessionCart);
    }

    private function syncSessionFromDBWishlist(Wishlist $wl): void
    {
        $items = WishlistItem::where('wishlist_id', $wl->id)->get();
        $sessionWL = [];

        foreach ($items as $wi) {
            $meta = $this->metaToArray($wi->meta);
            $pid  = $wi->product_id;

            $sessionWL[$pid] = [
                'id'           => $pid,
                'name'         => $meta['name']      ?? '',
                'qty'          => 1, // wishlist uses 1
                'price'        => (float)($meta['price'] ?? 0),
                'weight'       => $meta['weight']    ?? 0,
                'thumbnail'    => $meta['thumbnail'] ?? '',
                'seller_id'    => $meta['seller']['id'] ?? null,
                'seller_name'  => $meta['seller']['name'] ?? '',
                'store_name'   => $meta['seller']['store_name'] ?? '',
                'store_logo'   => $meta['seller']['store_logo'] ?? '',
                'store_url'    => $meta['seller']['store_url'] ?? '',
                'seller_email' => $meta['seller']['email'] ?? '',
                'seller_phone' => $meta['seller']['phone'] ?? '',
                'seller_address'=> $meta['seller']['address'] ?? '',
            ];
        }
        session()->put('shopping_wishlist', $sessionWL);
    }

    /* =========================
     * Cart
     * ========================= */

    // Add to Cart (DB + session mirror)
    public function AddToCart($id, $qty)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['msgType' => 'error', 'msg' => __('Product not found')], 404);
        }
        $seller  = User::find($product->user_id);
        $cart    = $this->ensureCart();

        $quantity  = (int)($qty == 0 ? 1 : $qty);
        $cartItem  = CartItem::where('cart_id', $cart->id)->where('product_id', $id)->first();

        if ($cartItem) {
            $cartItem->quantity = (int)$cartItem->quantity + $quantity;
            $cartItem->save();
        } else {
            CartItem::create([
                'cart_id'    => $cart->id,
                'product_id' => $product->id,
                'quantity'   => $quantity,
                'unit_price' => $product->sale_price,
                'meta'       => array_merge(
                    $this->snapshot($product, $seller),
                    ['price' => $product->sale_price] // handy for wishlist mirror too
                ),
            ]);
        }

        // keep session in sync for current frontend
        $this->syncSessionFromDBCart($cart);

        return response()->json(['msgType' => 'success', 'msg' => __('New Data Added Successfully')]);
    }

    // Mini-cart view payload (from DB, not session)
    public function ViewCart()
    {
        $gtext   = gtext();
        $gtax    = getTax();
        $taxRate = $gtax['percentage'];
        $Path    = asset('public/media');

        $cart = $this->ensureCart();
        $items = CartItem::where('cart_id', $cart->id)->get();

        $count = 0;
        $Total_Price = 0;
        $Sub_Total   = 0;
        $htmlItems = '';

        foreach ($items as $ci) {
            $meta = $this->metaToArray($ci->meta);
            $qty  = (int)$ci->quantity;
            $priceEach = (float)$ci->unit_price;

            $count += $qty;
            $Total_Price += $priceEach * $qty;
            $Sub_Total   += $priceEach * $qty;

            $name   = $meta['name'] ?? '';
            $thumb  = $meta['thumbnail'] ?? '';
            $pid    = $ci->product_id;

            if ($gtext['currency_position'] == 'left') {
                $price = '<span id="product-quatity">'.$qty.'</span> x '.$gtext['currency_icon'].$priceEach;
            } else {
                $price = '<span id="product-quatity">'.$qty.'</span> x '.$priceEach.$gtext['currency_icon'];
            }

            $htmlItems .= '<li>
                <div class="cart-item-card">
                    <a data-id="'.$pid.'" id="removetocart_'.$pid.'" onclick="onRemoveToCart('.$pid.')" href="javascript:void(0);" class="item-remove"><i class="bi bi-x"></i></a>
                    <div class="cart-item-img">
                        <img src="'.$Path.'/'.$thumb.'" alt="'.e($name).'" />
                    </div>
                    <div class="cart-item-desc">
                        <h6><a href="'.route('frontend.product', [$pid, Str::slug($name)]).'">'.e($name).'</a></h6>
                        <p>'.$price.'</p>
                    </div>
                </div>
            </li>';
        }

        $TotalPrice = NumberFormat($Total_Price);
        $SubTotal   = NumberFormat($Sub_Total);
        $TaxCal     = ($Total_Price * $taxRate) / 100;
        $tax        = NumberFormat($TaxCal);
        $total      = $Sub_Total + $TaxCal;
        $GrandTotal = NumberFormat($total);

        $datalist = [];
        $datalist['items']     = $htmlItems;
        $datalist['total_qty'] = $count;

        if ($gtext['currency_position'] == 'left') {
            $datalist['sub_total']   = $gtext['currency_icon'].$SubTotal;
            $datalist['tax']         = $gtext['currency_icon'].$tax;
            $datalist['price_total'] = $gtext['currency_icon'].$TotalPrice;
            $datalist['total']       = $gtext['currency_icon'].$GrandTotal;
        } else {
            $datalist['sub_total']   = $SubTotal.$gtext['currency_icon'];
            $datalist['tax']         = $tax.$gtext['currency_icon'];
            $datalist['price_total'] = $TotalPrice.$gtext['currency_icon'];
            $datalist['total']       = $GrandTotal.$gtext['currency_icon'];
        }

        return response()->json($datalist);
    }

    // Remove line from cart (by product_id)
    public function RemoveToCart($rowid)
    {
        $cart = $this->ensureCart();
        CartItem::where('cart_id', $cart->id)->where('product_id', $rowid)->delete();

        $this->syncSessionFromDBCart($cart);

        return response()->json(['msgType' => 'success', 'msg' => __('Data Removed Successfully')]);
    }

    // Cart page
    public function getCart()
    {
        return view('frontend.cart');
    }

    // Totals payload for cart page
    public function getViewCartData()
    {
        $gtext   = gtext();
        $gtax    = getTax();
        $taxRate = $gtax['percentage'];

        $cart = $this->ensureCart();
        $items = CartItem::where('cart_id', $cart->id)->get();

        $count = 0;
        $Total_Price = 0;
        $Sub_Total   = 0;

        foreach ($items as $ci) {
            $qty = (int)$ci->quantity;
            $priceEach = (float)$ci->unit_price;

            $count       += $qty;
            $Total_Price += $priceEach * $qty;
            $Sub_Total   += $priceEach * $qty;
        }

        $TotalPrice = NumberFormat($Total_Price);
        $SubTotal   = NumberFormat($Sub_Total);
        $TaxCal     = ($Total_Price * $taxRate) / 100;
        $tax        = NumberFormat($TaxCal);
        $total      = $Sub_Total + $TaxCal;
        $GrandTotal = NumberFormat($total);
        $discount   = 0;

        $datalist = [];
        $datalist['total_qty'] = $count;

        if ($gtext['currency_position'] == 'left') {
            $datalist['sub_total']   = $gtext['currency_icon'].$SubTotal;
            $datalist['tax']         = $gtext['currency_icon'].$tax;
            $datalist['price_total'] = $gtext['currency_icon'].$TotalPrice;
            $datalist['total']       = $gtext['currency_icon'].$GrandTotal;
            $datalist['discount']    = $gtext['currency_icon'].$discount;
        } else {
            $datalist['sub_total']   = $SubTotal.$gtext['currency_icon'];
            $datalist['tax']         = $tax.$gtext['currency_icon'];
            $datalist['price_total'] = $TotalPrice.$gtext['currency_icon'];
            $datalist['total']       = $GrandTotal.$gtext['currency_icon'];
            $datalist['discount']    = $discount.$gtext['currency_icon'];
        }

        return response()->json($datalist);
    }

    // -1 quantity
    public function DecreaseToCart($rowid)
    {
        $cart = $this->ensureCart();
        $ci = CartItem::where('cart_id', $cart->id)->where('product_id', $rowid)->first();

        if (!$ci) {
            return response()->json(['msgType' => 'error', 'msg' => __('Item not found in cart.')], 404);
        }

        if ((int)$ci->quantity > 1) {
            $ci->quantity = (int)$ci->quantity - 1;
            $ci->save();
            $msg = __('Quantity updated.');
        } else {
            $ci->delete();
            $msg = __('Item removed from cart.');
        }

        $this->syncSessionFromDBCart($cart);
        return response()->json(['msgType' => 'success', 'msg' => $msg]);
    }

    // +1 quantity
    public function IncreaseToCart($rowid)
    {
        return $this->AddToCart($rowid, 1);
    }

    // Set exact qty (>=1)
    public function UpdateCartQty($rowid, $qty)
    {
        $qty = (int)$qty;
        if ($qty < 1) {
            return response()->json(['msgType'=>'error','msg'=>__('Quantity must be at least 1.')], 422);
        }

        $cart = $this->ensureCart();
        $ci = CartItem::where('cart_id', $cart->id)->where('product_id', $rowid)->first();

        if (!$ci) {
            return response()->json(['msgType'=>'error','msg'=>__('Item not found in cart.')], 404);
        }

        $ci->quantity = $qty;
        $ci->save();

        $this->syncSessionFromDBCart($cart);

        return response()->json(['msgType'=>'success','msg'=>__('Quantity updated.')]);
    }

    /* =========================
     * Wishlist
     * ========================= */

    public function addToWishlist($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['msgType' => 'error', 'msg' => __('Product not found')], 404);
        }
        $seller   = User::find($product->user_id);
        $wishlist = $this->ensureWishlist();

        $existing = WishlistItem::where('wishlist_id', $wishlist->id)->where('product_id', $id)->first();
        if (!$existing) {
            WishlistItem::create([
                'wishlist_id' => $wishlist->id,
                'product_id'  => $product->id,
                'meta'        => array_merge(
                    $this->snapshot($product, $seller),
                    ['price' => $product->sale_price, 'qty' => 1]
                ),
            ]);
        }

        $this->syncSessionFromDBWishlist($wishlist);

        return response()->json(['msgType' => 'success', 'msg' => __('New Data Added Successfully')]);
    }

    public function getWishlist()
    {
        return view('frontend.wishlist');
    }

    public function RemoveToWishlist($rowid)
    {
        $wishlist = $this->ensureWishlist();
        WishlistItem::where('wishlist_id', $wishlist->id)->where('product_id', $rowid)->delete();

        $this->syncSessionFromDBWishlist($wishlist);

        return response()->json(['msgType' => 'success', 'msg' => __('Data Removed Successfully')]);
    }

    public function countWishlist()
    {
        $wishlist = $this->ensureWishlist();
        $count = WishlistItem::where('wishlist_id', $wishlist->id)->count();
        return response()->json($count);
    }

    public function MoveWishlistToCart($id, $qty = 1)
    {
        $qty = max(1, (int)$qty);
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['msgType'=>'error','msg'=>__('Product not found')], 404);
        }
        $seller   = User::find($product->user_id);
        $cart     = $this->ensureCart();
        $wishlist = $this->ensureWishlist();

        // add/update cart item
        $ci = CartItem::where('cart_id', $cart->id)->where('product_id', $id)->first();
        if ($ci) {
            $ci->quantity = (int)$ci->quantity + $qty;
            $ci->save();
        } else {
            CartItem::create([
                'cart_id'    => $cart->id,
                'product_id' => $product->id,
                'quantity'   => $qty,
                'unit_price' => $product->sale_price,
                'meta'       => array_merge($this->snapshot($product, $seller), ['price' => $product->sale_price]),
            ]);
        }

        // remove from wishlist
        WishlistItem::where('wishlist_id', $wishlist->id)->where('product_id', $id)->delete();

        // sync session mirrors
        $this->syncSessionFromDBCart($cart);
        $this->syncSessionFromDBWishlist($wishlist);

        return response()->json(['msgType' => 'success', 'msg' => __('Moved to cart.')]);
    }
}
