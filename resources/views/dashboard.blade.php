<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                    <div class="mt-8 rounded bg-white shadow overflow-hidden">
                        <div class="p-4">
                            <div class="flex">
                                <div class="w-1/3 px-3 pt-6 pb-3 text-2xl text-indigo-600 font-semibold">Name</div>
                            </div>
                        @foreach ($users as $user)
                                <div class="flex border-t">
                                    <div class="w-1/3 px-3 text-gray-800 flex items-center">
                                        <div class="py-4">{{ $user->name }}</div>
                                        @if ($user->is_friend_of_user)
                                            <div class="ml-2 px-2 py-1 text-xs text-yellow-800 font-semibold bg-yellow-500 rounded-full">
                                                Friend
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="bg-gray-200 p-4">
                            {!! $users->links() !!}
                        </div>
                    </div>

        </div>
    </div>
</x-app-layout>
