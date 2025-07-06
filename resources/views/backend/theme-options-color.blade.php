@extends('layouts.backend')

@section('title', __('Color'))

@section('content')
<!-- main Section -->
<div class="main-body">
	<div class="container-fluid">
		@php $vipc = vipc(); @endphp
		@if($vipc['bkey'] == 0) 
		@include('backend.partials.vipc')
		@else
		<div class="row mt-25">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="col-lg-12">
								{{ __('Color') }}
							</div>
						</div>
					</div>
					<div class="card-body tabs-area p-0">
						@include('backend.partials.theme_options_tabs_nav')
						<div class="tabs-body">
							<!--Data Entry Form-->
							<form novalidate="" data-validate="parsley" id="DataEntry_formId">
								<div class="row">
									<div class="col-lg-8">
										<div class="form-group">
											<label>{{ __('Theme color') }}<span class="red">*</span></label>
											<div id="theme_color_picker" class="input-group tw-picker">
												<input name="theme_color" id="theme_color" type="text" value="{{ $datalist['theme_color'] == '' ? '#61a402' : $datalist['theme_color'] }}" class="form-control"/>
												<span class="input-group-addon"><i></i></span>
											</div>
										</div>
										
										<div class="form-group">
											<label>{{ __('Green Color') }}<span class="red">*</span></label>
											<div id="green_color_picker" class="input-group tw-picker">
												<input name="green_color" id="green_color" type="text" value="{{ $datalist['green_color'] == '' ? '#65971e' : $datalist['green_color'] }}" class="form-control"/>
												<span class="input-group-addon"><i></i></span>
											</div>
										</div>
										
										<div class="form-group">
											<label>{{ __('Light Green Color') }}<span class="red">*</span></label>
											<div id="light_green_color_picker" class="input-group tw-picker">
												<input name="light_green_color" id="light_green_color" type="text" value="{{ $datalist['light_green_color'] == '' ? '#daeac5' : $datalist['light_green_color'] }}" class="form-control"/>
												<span class="input-group-addon"><i></i></span>
											</div>
										</div>
										
										<div class="form-group">
											<label>{{ __('Lightness Green Color') }}<span class="red">*</span></label>
											<div id="lightness_green_color_picker" class="input-group tw-picker">
												<input name="lightness_green_color" id="lightness_green_color" type="text" value="{{ $datalist['lightness_green_color'] == '' ? '#fdfff8' : $datalist['lightness_green_color'] }}" class="form-control"/>
												<span class="input-group-addon"><i></i></span>
											</div>
										</div>
										
										<div class="form-group">
											<label>{{ __('Gray Color') }}<span class="red">*</span></label>
											<div id="gray_color_picker" class="input-group tw-picker">
												<input name="gray_color" id="gray_color" type="text" value="{{ $datalist['gray_color'] == '' ? '#8d949d' : $datalist['gray_color'] }}" class="form-control"/>
												<span class="input-group-addon"><i></i></span>
											</div>
										</div>
										
										<div class="form-group">
											<label>{{ __('Dark Gray Color') }}<span class="red">*</span></label>
											<div id="dark_gray_color_picker" class="input-group tw-picker">
												<input name="dark_gray_color" id="dark_gray_color" type="text" value="{{ $datalist['dark_gray_color'] == '' ? '#595959' : $datalist['dark_gray_color'] }}" class="form-control"/>
												<span class="input-group-addon"><i></i></span>
											</div>
										</div>
										
										<div class="form-group">
											<label>{{ __('Light Gray Color') }}<span class="red">*</span></label>
											<div id="light_gray_color_picker" class="input-group tw-picker">
												<input name="light_gray_color" id="light_gray_color" type="text" value="{{ $datalist['light_gray_color'] == '' ? '#e7e7e7' : $datalist['light_gray_color'] }}" class="form-control"/>
												<span class="input-group-addon"><i></i></span>
											</div>
										</div>
										
										<div class="form-group">
											<label>{{ __('Black Color') }}<span class="red">*</span></label>
											<div id="black_color_picker" class="input-group tw-picker">
												<input name="black_color" id="black_color" type="text" value="{{ $datalist['black_color'] == '' ? '#232424' : $datalist['black_color'] }}" class="form-control"/>
												<span class="input-group-addon"><i></i></span>
											</div>
										</div>
										
										<div class="form-group">
											<label>{{ __('White Color') }}<span class="red">*</span></label>
											<div id="white_color_picker" class="input-group tw-picker">
												<input name="white_color" id="white_color" type="text" value="{{ $datalist['white_color'] == '' ? '#ffffff' : $datalist['white_color'] }}" class="form-control"/>
												<span class="input-group-addon"><i></i></span>
											</div>
										</div>
										
									</div>
									<div class="col-lg-4"></div>
								</div>
								
								<div class="row tabs-footer mt-15">
									<div class="col-lg-12">
										<a id="submit-form" href="javascript:void(0);" class="btn blue-btn">{{ __('Save') }}</a>
									</div>
								</div>
							</form>
							<!--/Data Entry Form/-->
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
<!-- css/js -->
<link rel="stylesheet" href="{{asset('public/backend/bootstrap-colorpicker/bootstrap-colorpicker.min.css')}}">
<script src="{{asset('public/backend/bootstrap-colorpicker/bootstrap-colorpicker.min.js')}}"></script>
<script src="{{asset('public/backend/pages/theme_option_color.js')}}"></script>
@endpush