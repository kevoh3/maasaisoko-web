@extends('layouts.backend')
@section('title', __('Customer Carts'))

@section('content')
    <div class="main-body">
        <div class="container-fluid">
            <div class="row mt-25">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">{{ __('Customer Carts') }}</div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('User') }}</th>
                                    <th>{{ __('Session') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Updated') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($carts as $cart)
                                    <tr>
                                        <td>#{{ $cart->id }}</td>
                                        <td>{{ optional($cart->user)->name ?? '—' }}</td>
                                        <td><code>{{ Str::limit($cart->session_id, 18) }}</code></td>
                                        <td>{{ $cart->status }}</td>
                                        <td>{{ $cart->updated_at->format('Y-m-d H:i') }}</td>
                                        <td><a class="btn btn-sm btn-primary" href="{{ route('backend.carts.show', $cart) }}">{{ __('View') }}</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center">{{ __('No carts found.') }}</td></tr>
                                @endforelse
                                </tbody>
                            </table>

                            {{ $carts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
