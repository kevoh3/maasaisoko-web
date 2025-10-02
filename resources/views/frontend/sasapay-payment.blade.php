@extends('layouts.frontend')

@section('title', 'SasaPay Payment - MaasaiSoko')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-credit-card"></i> Complete Your Payment with SasaPay
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Order Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Order Details</h6>
                            <p><strong>Order #:</strong> {{ $order->order_no }}</p>
                            <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge badge-warning">{{ ucfirst($order->order_status) }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Payment Summary</h6>
                            <p><strong>Subtotal:</strong> KES {{ number_format($order->subtotal, 2) }}</p>
                            <p><strong>Shipping:</strong> KES {{ number_format($order->shipping_cost, 2) }}</p>
                            <p><strong>Tax:</strong> KES {{ number_format($order->tax, 2) }}</p>
                            <hr>
                            <p><strong>Total Amount:</strong> KES {{ number_format($order->total_amount, 2) }}</p>
                        </div>
                    </div>

                    <!-- Error Messages -->
                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                        </div>
                    @endif

                    <!-- Payment Form -->
                    <form id="sasapay-form" method="POST" action="{{ route('frontend.sasapay.initiate') }}">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        
                        <div class="form-group">
                            <label for="payer_email">Email Address <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control @error('payer_email') is-invalid @enderror" 
                                   id="payer_email" 
                                   name="payer_email" 
                                   value="{{ old('payer_email', auth()->user()->email ?? '') }}" 
                                   required>
                            @error('payer_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="phone_number">Phone Number (Optional)</label>
                            <input type="tel" 
                                   class="form-control" 
                                   id="phone_number" 
                                   name="phone_number" 
                                   value="{{ old('phone_number', auth()->user()->phone ?? '') }}"
                                   placeholder="+254 700 000 000">
                        </div>

                        <!-- Payment Methods Info -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Payment Options Available:</h6>
                            <ul class="mb-0">
                                <li><strong>SasaPay Wallet</strong> - Pay using your SasaPay account</li>
                                <li><strong>M-Pesa</strong> - Pay via M-Pesa mobile money</li>
                                <li><strong>Airtel Money</strong> - Pay via Airtel Money</li>
                                <li><strong>T-KASH</strong> - Pay via T-KASH mobile money</li>
                                <li><strong>Card Payment</strong> - Pay using Visa/Mastercard</li>
                            </ul>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg btn-block" id="pay-button">
                                <i class="fas fa-lock"></i> 
                                <span id="button-text">Pay KES {{ number_format($order->total_amount, 2) }} with SasaPay</span>
                                <span id="button-loading" class="d-none">
                                    <i class="fas fa-spinner fa-spin"></i> Processing...
                                </span>
                            </button>
                        </div>
                    </form>

                    <!-- Back to Order -->
                    <div class="text-center mt-3">
                        <a href="{{ route('frontend.order-tracking', ['order_no' => $order->order_no]) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Order Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <h5>Processing Payment...</h5>
                <p class="text-muted">Please wait while we redirect you to SasaPay</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
console.log('SasaPay script loading...');
console.log('jQuery available:', typeof $ !== 'undefined');

$(document).ready(function() {
    console.log('SasaPay document ready');
    console.log('Pay button found:', $('#pay-button').length);
    console.log('Form found:', $('#sasapay-form').length);
    // Handle form submission
    $('#sasapay-form').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        processPayment();
    });
    
    // Handle button click as backup
    $('#pay-button').on('click', function(e) {
        console.log('Pay button clicked!');
        e.preventDefault();
        e.stopPropagation();
        processPayment();
    });
    
    function processPayment() {
        // Show loading state
        $('#pay-button').prop('disabled', true);
        $('#button-text').addClass('d-none');
        $('#button-loading').removeClass('d-none');
        $('#loadingModal').modal('show');
        
        // Submit form via AJAX
        console.log('Submitting SasaPay form...');
        $.ajax({
            url: $('#sasapay-form').attr('action'),
            method: 'POST',
            data: $('#sasapay-form').serialize(),
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('SasaPay response:', response);
                if (response.success && response.checkout_url) {
                    console.log('Redirecting to:', response.checkout_url);
                    // Redirect to SasaPay checkout
                    window.location.href = response.checkout_url;
                } else {
                    // Show error message
                    $('#loadingModal').modal('hide');
                    alert('Payment initiation failed: ' + (response.message || 'Unknown error'));
                    resetButton();
                }
            },
            error: function(xhr) {
                $('#loadingModal').modal('hide');
                let errorMessage = 'Payment initiation failed. Please try again.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                alert(errorMessage);
                resetButton();
            }
        });
    }
    
    function resetButton() {
        $('#pay-button').prop('disabled', false);
        $('#button-text').removeClass('d-none');
        $('#button-loading').addClass('d-none');
    }
});
</script>
@endsection