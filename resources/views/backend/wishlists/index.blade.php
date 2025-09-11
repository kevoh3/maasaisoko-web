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
                                @forelse($wishlists as $wishlist)
                                    <tr>
                                        <td>#{{ $wishlist->id }}</td>
                                        <td>{{ optional($wishlist->user)->name ?? '—' }}</td>
                                        <td><code>{{ Str::limit($wishlist->session_id, 18) }}</code></td>
                                        <td>{{ $wishlist->status }}</td>
                                        <td>{{ $wishlist->updated_at->format('Y-m-d H:i') }}</td>
                                        <td><a class="btn btn-sm btn-primary" href="{{ route('backend.wishlists.show', $wishlist) }}">{{ __('View') }}</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center">{{ __('No wishlists found.') }}</td></tr>
                                @endforelse
                                </tbody>
                            </table>

                            {{ $wishlists->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
