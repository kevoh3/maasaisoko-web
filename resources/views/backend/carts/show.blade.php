@extends('layouts.backend')
@section('title', __('Cart #').$cart->id)

@section('content')
    <div class="main-body">
        <div class="container-fluid">
            <div class="row mt-25">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <span>{{ __('Cart #') }}{{ $cart->id }}</span>
                            <div class="float-right">
                                <a href="{{ route('backend.carts.index') }}" class="btn warning-btn btn-list">
                                    <i class="fa fa-reply"></i> {{ __('Back to List') }}
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <p><strong>{{ __('User') }}:</strong> {{ optional($cart->user)->name ?? 'Guest' }}</p>
                            <p><strong>{{ __('Session') }}:</strong> <code>{{ $cart->session_id }}</code></p>
                            <p><strong>{{ __('Status') }}:</strong> {{ $cart->status }}</p>

                            <h5 class="mt-3">{{ __('Items') }}</h5>
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th class="text-right">{{ __('Qty') }}</th>
                                    <th class="text-right">{{ __('Unit Price') }}</th>
                                    <th class="text-right">{{ __('Subtotal') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($cart->items as $item)
                                    <tr>
                                        <td>
                                            {{ $item->product->title ?? ($item->meta['name'] ?? '—') }}
                                            @if(!empty($item->meta['thumbnail']))
                                                <br>
                                                <img src="{{ asset('public/media/'.$item->meta['thumbnail']) }}" alt="" style="height:36px">
                                            @endif
                                        </td>
                                        <td class="text-right">{{ $item->quantity }}</td>
                                        <td class="text-right">{{ NumberFormat($item->unit_price) }}</td>
                                        <td class="text-right">{{ NumberFormat($item->quantity * $item->unit_price) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">{{ __('No items.') }}</td></tr>
                                @endforelse
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
