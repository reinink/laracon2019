@extends('layout')

@section('content')

<div class="mt-8 rounded bg-white shadow overflow-hidden">
    <div class="p-4">
        <div class="flex">
            <div class="w-1/3 px-3 pt-6 pb-3 text-2xl text-green-600 font-semibold">Name</div>
            <div class="w-1/3 px-3 pt-6 pb-3 text-2xl text-green-600 font-semibold">Club</div>
        </div>
        @foreach ($users as $user)
            <div class="flex border-t">
                <div class="w-1/3 px-3 text-gray-800 flex items-center">
                    <div class="py-4">{{ $user->name }}</div>
                    @if (Auth::user()->buddies->contains($user))
                        <div class="ml-2 px-2 py-1 text-xs text-yellow-800 font-semibold bg-yellow-500 rounded-full">
                            Buddy
                        </div>
                    @endif
                </div>
                <div class="w-1/3 px-3 py-4 text-gray-800">{{ $user->club->name }}</div>
            </div>
        @endforeach
    </div>
    <div class="bg-gray-200 p-4">
        {!! $users->links() !!}
    </div>
</div>

@endsection
