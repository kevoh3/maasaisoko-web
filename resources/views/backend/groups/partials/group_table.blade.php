<div class="table-responsive">
    <table class="table table-borderless table-theme" style="width:100%;">
        <thead>
        <tr>
            <th class="checkboxlist text-center" style="width:5%">
                <input class="tp-check-all checkAll" type="checkbox">
            </th>
            <th class="text-center" style="width:5%">{{ __('Logo') }}</th>
            <th style="width:22%">{{ __('Name') }}</th>
            <th style="width:18%">{{ __('Registration No.') }}</th>
            <th style="width:15%">{{ __('Phone') }}</th>
            <th style="width:15%">{{ __('Email') }}</th>
            <th class="text-center" style="width:8%">{{ __('Members') }}</th>
            <th class="text-center" style="width:7%">{{ __('Status') }}</th>
            <th class="text-center" style="width:5%">{{ __('Action') }}</th>
        </tr>
        </thead>

        <tbody>
        @if ($groups->count() > 0)
            @foreach($groups as $row)
                <tr>
                    {{-- Bulk checkbox --}}
                    <td class="checkboxlist text-center">
                        <input name="item_ids[]" value="{{ $row->id }}" class="tp-checkbox selected_item" type="checkbox">
                    </td>

                    {{-- Logo (fallback to placeholder) --}}
                    @php
                        $logo = $row->logo ?? $row->photo ?? null; // support either column name if you add one
                    @endphp
                    @if (!empty($logo))
                        <td class="text-center">
                            <div class="table_col_image">
                                <img src="{{ asset('public') }}/media/{{ $logo }}" alt="{{ $row->name }}">
                            </div>
                        </td>
                    @else
                        <td class="text-center">
                            <div class="table_col_image">
                                <img src="{{ asset('public') }}/backend/images/album_icon.png" alt="—">
                            </div>
                        </td>
                    @endif

                    {{-- Name --}}
                    <td class="text-left">
                        {{ $row->name }}
                        @if($row->type)
                            <div class="opacity50 small">{{ ucfirst($row->type) }}</div>
                        @endif
                    </td>

                    {{-- Registration No. --}}
                    <td class="text-left">{{ $row->registration_number ?? '—' }}</td>

                    {{-- Phone --}}
                    <td class="text-left">{{ $row->phone ?? '—' }}</td>

                    {{-- Email --}}
                    <td class="text-left">{{ $row->email ?? '—' }}</td>

                    {{-- Members (from withCount) --}}
                    <td class="text-center">{{ $row->members_count ?? 0 }}</td>

                    {{-- Status badge (active | pending | suspended | inactive) --}}
                    <td class="text-center">
                        @if($row->status === 'active')
                            <span class="enable_btn">{{ __('Active') }}</span>
                        @elseif($row->status === 'pending')
                            <span class="disable_btn">{{ __('Pending') }}</span>
                        @elseif($row->status === 'suspended')
                            <span class="disable_btn">{{ __('Suspended') }}</span>
                        @else
                            <span class="disable_btn">{{ __('Inactive') }}</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="text-center">
                        <div class="btn-group action-group">
                            <a class="action-btn" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-ellipsis-v"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a onclick="onEdit({{ $row->id }})" class="dropdown-item" href="javascript:void(0);">{{ __('Edit') }}</a>
                                <a onclick="onDelete({{ $row->id }})" class="dropdown-item" href="javascript:void(0);">{{ __('Delete') }}</a>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="9">{{ __('No data available') }}</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="row mt-15">
    <div class="col-lg-12 users_pagination">
        {{ $groups->links() }}
    </div>
</div>
