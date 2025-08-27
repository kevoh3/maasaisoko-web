<div class="table-responsive">
	<table class="table table-borderless table-theme" style="width:100%;">
		<thead>
			<tr>
				<th class="checkboxlist text-center" style="width:5%"><input class="tp-check-all checkAll" type="checkbox"></th>
				<th class="text-center" style="width:5%">{{ __('Logo') }} </th>

                <th style="width:15%">{{ __('Name') }}</th>
                <th style="width:15%">{{ __('Shop Name') }}</th>
                <th style="width:10%">{{ __('Shop Phone') }}</th>
                <th style="width:15%">{{ __('Email') }}</th>
                <th style="width:10%">{{ __('KYC Status') }}</th>

                <th style="width:12%">{{ __('Package') }}</th>
                <th style="width:10%">{{ __('Billing Cycle') }}</th>
                <th style="width:10%">{{ __('Price') }}</th>
                <th style="width:12%">{{ __('Expires At') }}</th>


                <th class="text-center" style="width:10%">{{ __('Status') }}</th>
                <th class="text-center" style="width:10%">{{ __('Action') }}</th>
			</tr>
		</thead>
		<tbody>
			@if (count($datalist)>0)
			@foreach($datalist as $row)
			<tr>
				<td class="checkboxlist text-center"><input name="item_ids[]" value="{{ $row->id }}" class="tp-checkbox selected_item" type="checkbox"></td>

				@if ($row->photo != '')
				<td class="text-center"><div class="table_col_image"><img src="{{ asset('public') }}/media/{{ $row->photo }}" /></div></td>
				@else
				<td class="text-center"><div class="table_col_image"><img src="{{ asset('public') }}/backend/images/album_icon.png" /></div></td>
				@endif
				<td class="text-left">{{ $row->name }}</td>
				<td class="text-left">{{ $row->shop_name }}</td>
				<td class="text-left">{{ $row->phone }}</td>
				<td class="text-left">{{ $row->email }}</td>


{{--                <td class="text-center">--}}
{{--                    {{ $row->currentPackage()?->name ?? '—' }}--}}
{{--                </td>--}}

                <td>@if ($row->kyc_status === 'verified')
                    <span class="enable_btn">Verified</span>
                @else
                    <span class="disable_btn">{{ ucfirst($row->kyc_status) }}</span>
                @endif</td>
                <td class="text-center">
                    {{ $row->currentSubscription?->package?->name ?? '—' }}
                </td>

                <td class="text-center">
                    {{ $row->currentSubscription?->billing_cycle ? ucfirst($row->currentSubscription->billing_cycle) : '—' }}
                </td>

                <td class="text-center">
                    @php
                        $sub = $row->currentSubscription;
                    @endphp
                    {{ $sub ? number_format((float)$sub->price, 2) . ' ' . ($sub->currency ?? 'KES') : '—' }}
                </td>

                <td class="text-center">
                    {{ optional($row->currentSubscription?->expires_at)->format('Y-m-d') ?? '—' }}
                </td>
                @if ($row->status_id == 1)
                    <td class="text-center"><span class="enable_btn">{{ $row->status }}</span></td>
                @else
                    <td class="text-center"><span class="disable_btn">{{ $row->status }}</span></td>
                @endif

				<td class="text-center">
					<div class="btn-group action-group">
						<a class="action-btn" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
						<div class="dropdown-menu dropdown-menu-right">
							<a onclick="onEdit({{ $row->id }})" class="dropdown-item" href="javascript:void(0);">{{ __('View') }}</a>
							<a onclick="onDelete({{ $row->id }})" class="dropdown-item" href="javascript:void(0);">{{ __('Suspend') }}</a>
						</div>
					</div>
				</td>
			</tr>
			@endforeach
			@else
			<tr>
				<td class="text-center" colspan="8">{{ __('No data available') }}</td>
			</tr>
			@endif
		</tbody>
	</table>
</div>
<div class="row mt-15">
	<div class="col-lg-12 users_pagination">
		{{ $datalist->links() }}
	</div>
</div>
