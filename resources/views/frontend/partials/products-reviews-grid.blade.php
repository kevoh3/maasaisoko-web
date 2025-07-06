
<div class="review-body">
	@foreach($pro_reviews as $row)
	<div class="review-card">
		<div class="review-photo">
			@if($row->photo != '')
			<img src="{{ asset('public/media/'.$row->photo) }}" alt="{{ $row->name }}" />
			@else
			<span class="username">{{ strtoupper(sub_str($row->name, 0, 1)) }}</span>
			@endif
		</div>
		<div class="review-info">
			<div class="author-name">{{ $row->name }}</div>
			<div class="rating-wrap">
				<div class="stars-outer">
					<div class="stars-inner" style="width:{{ $row->rating }}%;"></div>
				</div>
			</div>
			<div class="date">{{ date('d-m-Y', strtotime($row->created_at)) }}</div>
			<div class="desc">
				<p>{{ $row->comments }}</p>
			</div>
		</div>
	</div>
	@endforeach
</div>
<div class="row mt-15">
	<div class="col-lg-12">
		{{ $pro_reviews->links() }}
	</div>
</div>
