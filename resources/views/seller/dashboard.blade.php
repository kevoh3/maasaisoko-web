@extends('layouts.backend')

@section('title', __('Dashboard'))

@section('content')
<!-- main Section -->
<div class="main-body">
	<div class="container-fluid">
		@php $vipc = vipc(); @endphp
		@if($vipc['bkey'] == 0)
		@include('seller.partials.vipc')
		@else
		<div class="row">
			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-3 mt-25">
				<div class="status-card bg-grad-1">
					<div class="status-text">
						<div class="status-name opacity50">{{ __('Total') }}</div>
						<div class="status-name opacity50">{{ __('Orders') }}</div>
						<h2 class="status-count">{{ $total_order }}</h2>
					</div>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
						<path fill="rgba(255,255,255,0.2)" fill-opacity="1" d="M0,32L34.3,58.7C68.6,85,137,139,206,138.7C274.3,139,343,85,411,53.3C480,21,549,11,617,10.7C685.7,11,754,21,823,42.7C891.4,64,960,96,1029,138.7C1097.1,181,1166,235,1234,218.7C1302.9,203,1371,117,1406,74.7L1440,32L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
					</svg>
				</div>
			</div>

			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-3 mt-25">
				<div class="status-card bg-grad-2">
					<div class="status-text">
						<div class="status-name opacity50">{{ __('Orders') }}</div>
						<div class="status-name opacity50">{{ __('Awaiting processing') }}</div>
						<h2 class="status-count">{{ $pending_order }}</h2>
					</div>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
						<path fill="rgba(255,255,255,0.2)" fill-opacity="1" d="M0,32L34.3,58.7C68.6,85,137,139,206,138.7C274.3,139,343,85,411,53.3C480,21,549,11,617,10.7C685.7,11,754,21,823,42.7C891.4,64,960,96,1029,138.7C1097.1,181,1166,235,1234,218.7C1302.9,203,1371,117,1406,74.7L1440,32L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
					</svg>
				</div>
			</div>

			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-3 mt-25">
				<div class="status-card bg-grad-3">
					<div class="status-text">
						<div class="status-name opacity50">{{ __('Orders') }}</div>
						<div class="status-name opacity50">{{ __('Processing') }}</div>
						<h2 class="status-count">{{ $processing_order }}</h2>
					</div>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
						<path fill="rgba(255,255,255,0.2)" fill-opacity="1" d="M0,32L34.3,58.7C68.6,85,137,139,206,138.7C274.3,139,343,85,411,53.3C480,21,549,11,617,10.7C685.7,11,754,21,823,42.7C891.4,64,960,96,1029,138.7C1097.1,181,1166,235,1234,218.7C1302.9,203,1371,117,1406,74.7L1440,32L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
					</svg>
				</div>
			</div>
			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-3 mt-25">
				<div class="status-card bg-grad-4">
					<div class="status-text">
						<div class="status-name opacity50">{{ __('Orders') }}</div>
						<div class="status-name opacity50">{{ __('Ready for pickup') }}</div>
						<h2 class="status-count">{{ $ready_for_pickup_order }}</h2>
					</div>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
						<path fill="rgba(255,255,255,0.2)" fill-opacity="1" d="M0,32L34.3,58.7C68.6,85,137,139,206,138.7C274.3,139,343,85,411,53.3C480,21,549,11,617,10.7C685.7,11,754,21,823,42.7C891.4,64,960,96,1029,138.7C1097.1,181,1166,235,1234,218.7C1302.9,203,1371,117,1406,74.7L1440,32L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
					</svg>
				</div>
			</div>
			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-3 mt-25">
				<div class="status-card bg-grad-5">
					<div class="status-text">
						<div class="status-name opacity50">{{ __('Orders') }}</div>
						<div class="status-name opacity50">{{ __('Completed') }}</div>
						<h2 class="status-count">{{ $completed_order }}</h2>
					</div>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
						<path fill="rgba(255,255,255,0.2)" fill-opacity="1" d="M0,32L34.3,58.7C68.6,85,137,139,206,138.7C274.3,139,343,85,411,53.3C480,21,549,11,617,10.7C685.7,11,754,21,823,42.7C891.4,64,960,96,1029,138.7C1097.1,181,1166,235,1234,218.7C1302.9,203,1371,117,1406,74.7L1440,32L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
					</svg>
				</div>
			</div>
			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-3 mt-25">
				<div class="status-card bg-grad-6">
					<div class="status-text">
						<div class="status-name opacity50">{{ __('Orders') }}</div>
						<div class="status-name opacity50">{{ __('Canceled') }}</div>
						<h2 class="status-count">{{ $canceled_order }}</h2>
					</div>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
						<path fill="rgba(255,255,255,0.2)" fill-opacity="1" d="M0,32L34.3,58.7C68.6,85,137,139,206,138.7C274.3,139,343,85,411,53.3C480,21,549,11,617,10.7C685.7,11,754,21,823,42.7C891.4,64,960,96,1029,138.7C1097.1,181,1166,235,1234,218.7C1302.9,203,1371,117,1406,74.7L1440,32L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
					</svg>
				</div>
			</div>
			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-3 mt-25">
				<div class="status-card bg-grad-7">
					<div class="status-text">
						<div class="status-name opacity50">{{ __('Total') }}</div>
						<div class="status-name opacity50">{{ __('Published Products') }}</div>
						<h2 class="status-count">{{ $published_product }}</h2>
					</div>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
						<path fill="rgba(255,255,255,0.2)" fill-opacity="1" d="M0,32L34.3,58.7C68.6,85,137,139,206,138.7C274.3,139,343,85,411,53.3C480,21,549,11,617,10.7C685.7,11,754,21,823,42.7C891.4,64,960,96,1029,138.7C1097.1,181,1166,235,1234,218.7C1302.9,203,1371,117,1406,74.7L1440,32L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
					</svg>
				</div>
			</div>

			<div class="col-sm-6 col-md-4 col-lg-3 col-xl-3 mt-25">
				<div class="status-card bg-grad-10">
					<div class="status-text">
						<div class="status-name opacity50">{{ __('Total') }}</div>
						<div class="status-name opacity50">{{ __('Review & Ratings') }}</div>
						<h2 class="status-count">{{ $review }}</h2>
					</div>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
						<path fill="rgba(255,255,255,0.2)" fill-opacity="1" d="M0,32L34.3,58.7C68.6,85,137,139,206,138.7C274.3,139,343,85,411,53.3C480,21,549,11,617,10.7C685.7,11,754,21,823,42.7C891.4,64,960,96,1029,138.7C1097.1,181,1166,235,1234,218.7C1302.9,203,1371,117,1406,74.7L1440,32L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
					</svg>
				</div>
			</div>
		</div>

            {{-- Seller Compliance Row --}}
            <div class="row mt-30">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-1">{{ __('KYC Verification') }}</h5>
                                @php
                                    $kyc = $kycStatus ?? 'not_submitted';
                                    $kycLabel = ucfirst(str_replace('_',' ', $kyc));
                                    $kycClass = match($kyc) {
                                        'verified' => 'badge-success',
                                        'pending'  => 'badge-info',
                                        'rejected' => 'badge-danger',
                                        default    => 'badge-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $kycClass }} px-3 py-2">{{ $kycLabel }}</span>
                            </div>
                            @if($kyc !== 'verified')
{{--                                {{ route('seller.kyc.start') }}--}}
                                <a href="#" class="btn btn-outline-primary btn-sm ml-3">
                                    {{ __('Complete KYC') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-1">{{ __('Registration Fee') }}</h5>
                                @if($regFeePaid ?? false)
                                    <span class="badge badge-success px-3 py-2">{{ __('Paid') }}</span>
                                    @if($user->registration_fee_amount)
                                        <small class="text-muted ml-2">
                                            {{ number_format($user->registration_fee_amount, 0) }} {{ $user->registration_fee_currency }}
                                        </small>
                                    @endif
                                @else
                                    <span class="badge badge-danger px-3 py-2">{{ __('Unpaid') }}</span>
                                    <small class="d-block text-muted mt-1">
                                        {{ __('Amount Due:') }}
                                        {{ number_format($regFeeAmount, 0) }} {{ $regFeeCurrency }}
                                        ({{ ucfirst($user->classification) }})
                                    </small>
                                @endif
                            </div>
                            @if(!($regFeePaid ?? false))
{{--                                {{ route('seller.registration.fee.pay') }}--}}
                                <a href="#"
                                   class="btn btn-outline-success btn-sm ml-3">
                                    {{ __('Pay Now') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>



            </div>

            @php
                $sub = auth()->user()->currentSubscription()->first();
                $pkg = $sub?->package;
            @endphp

            <div class="row mt-25">
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="mb-1">{{ __('Current Plan') }}: {{ $pkg->name ?? '—' }}</h5>
                                @if($pkg)
                                    <small class="text-muted">
                                        {{ __('Item limit') }}: {{ $pkg->items }} &middot;
                                        {{ __('Billing') }}: {{ ucfirst(str_replace('_',' ', $sub->billing_cycle)) }} &middot;
                                        {{ __('Next due') }}: {{ optional($sub->next_due_at)->format('Y-m-d') ?? '—' }}
                                    </small>
                                @else
                                    <small class="text-muted">{{ __('No active plan. You are on the Free tier.') }}</small>
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('seller.plans') }}" class="btn btn-outline-primary btn-sm">{{ __('Change Plan') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
			<div class="col-lg-6 mt-25">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="col-lg-12">
								<span>{{ __('Top 10 Selling Products') }}</span>
							</div>
						</div>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-borderless table-theme" style="width:100%;">
								<thead>
									<tr>
										<th class="text-left" style="width:80%">{{ __('Products') }}</th>
										<th class="text-center" style="width:20%">{{ __('Selling') }}</th>
									</tr>
								</thead>
								<tbody>
									@if (count($top_selling_products)>0)
									@foreach($top_selling_products as $row)
									<tr>
										<td class="text-left"><a href="{{ route('frontend.product', [$row->product_id, $row->slug]) }}">{{ $row->title }}</a></td>
										<td class="text-center">{{ $row->TotalSelling }}</td>
									</tr>
									@endforeach
									@else
									<tr>
										<td class="text-center" colspan="2">{{ __('No data available') }}</td>
									</tr>
									@endif
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6 mt-25">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="col-lg-12">
								<span>{{ __('Top 10 Rating Products') }}</span>
							</div>
						</div>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-borderless table-theme" style="width:100%;">
								<thead>
									<tr>
										<th class="text-left" style="width:70%">{{ __('Products') }}</th>
										<th class="text-center" style="width:15%">{{ __('Reviews') }}</th>
										<th class="text-center" style="width:15%">{{ __('Ratings') }}</th>
									</tr>
								</thead>
								<tbody>
									@if (count($top_rating_products)>0)
									@foreach($top_rating_products as $row)
									<tr>
										<td class="text-left"><a href="{{ route('frontend.product', [$row->item_id, $row->slug]) }}">{{ $row->title }}</a></td>
										<td class="text-center">{{ $row->TotalReview }}</td>
										<td class="text-center">{{ $row->TotalRating }}</td>
									</tr>
									@endforeach
									@else
									<tr>
										<td class="text-center" colspan="3">{{ __('No data available') }}</td>
									</tr>
									@endif
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		@endif
	</div>
</div>
<!-- /main Section -->
@endsection

@push('scripts')

@endpush
