<table class="table table-bordered">
    <thead>
    <tr>
        <th>{{ __('Date') }}</th>
        <th>{{ __('Code') }}</th>
        <th>{{ __('Amount') }}</th>
        <th>{{ __('Balance') }}</th>
        <th>{{ __('Type') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Party B') }}</th>
        <th>{{ __('Channel') }}</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($transactions as $txn)
        <tr>
            <td>{{ $txn->transaction_date }}</td>
            <td>{{ $txn->transaction_code }}</td>
            <td>{{ number_format($txn->amount, 2) }}</td>
            <td>{{ number_format($txn->running_balance, 2) }}</td>
            <td>{{ ucfirst($txn->type) }}</td>
            <td>{{ ucfirst($txn->status) }}</td>
            <td>
                {{ $txn->party_b_name }}<br>
                <small>{{ $txn->party_b_account_number }} - {{ $txn->party_b_platform }}</small>
            </td>
            <td>{{ $txn->channel }}</td>
        </tr>
    @empty
        <tr><td colspan="8" class="text-center">{{ __('No transactions found.') }}</td></tr>
    @endforelse
    </tbody>
</table>
