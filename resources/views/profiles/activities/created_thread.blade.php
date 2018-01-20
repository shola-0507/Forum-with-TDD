@component('profiles.activities.activity')

	@slot('heading')
		{{ $ProfileUser->name }} published 
		<a href="{{ $activity->subject->path() }}">
			"{{ $activity->subject->title }}"
		</a>
	@endslot

	@slot('body')
		{{ $activity->subject->body }}
	@endslot

@endcomponent
