@component('profiles.activities.activity')

	@slot('heading')
		 
		<a href="{{ $activity->subject->favourited->path() }}">
			{{ $ProfileUser->name }} favourited a reply
		</a>

		{{--  {{ $activity->subject->thread->title }}  --}}

	@endslot

	@slot('body')
		{{ $activity->subject->favourited->body }}
	@endslot

@endcomponent
