@extends('layouts.backend')

@section('title', __('Groups'))

@section('content')
    <!-- main Section -->
    <div class="main-body">
        <div class="container-fluid">
        <h1>Packages</h1>
        <a href="{{ route('backend.packages.create') }}" class="btn btn-primary mb-3">Add Package</a>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Name</th>
                <th>Items</th>
                <th>Monthly Price</th>
                <th>Quarterly Total</th>
                <th>Bi-Annual Total</th>
                <th>Annual Total</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($packages as $package)
                <tr>
                    <td>{{ $package->name }}</td>
                    <td>{{ $package->items }}</td>
                    <td>{{ number_format($package->base_monthly_price, 2) }}</td>
                    <td>{{ number_format($package->quarterly_total, 2) }}</td>
                    <td>{{ number_format($package->bi_annual_total, 2) }}</td>
                    <td>{{ number_format($package->annual_total, 2) }}</td>
                    <td>
                        <a href="{{ route('packages.edit', $package) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('packages.destroy', $package) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this package?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    </div>
@endsection
