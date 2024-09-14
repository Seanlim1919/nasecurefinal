<x-layout>
    <x-sidebar />
    <div>
        @if (session('success'))
            <x-flashMsg msg="{{ session('success') }}" bg="bg-yellow-500" />
        @elseif(session('deleted'))
            <x-flashMsg msg="{{ session('deleted') }}" bg="bg-red-500" />
        @endif
    </div>
    <div class="container mx-auto p-5 bg-gray-200 rounded-3xl">
        <div class="items-center mb-3">
            <h1 class="text-xl font-bold ps-4 mb-4">Schedules</h1>
            <p class="text-sm ps-8">Schedule logs of instructors.</p>
        </div>

        <div class="flex justify-end">
            @if (Auth::user()->role->name === 'instructor')
                <a href="{{ route('schedules.create') }}"
                    class="ml-2 px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-600 flex items-center focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                    </svg>
                    Add Schedule
                </a>
            @endif
        </div>

        <div class="w-full mt-6">
            <form method="GET" action="{{ route('schedules.index') }}" class="flex items-center">
                <div class="relative flex w-full">
                    <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full">
                    <button type="submit"
                        class="absolute right-0 top-0 h-full px-4 bg-blue-700 text-white rounded-r-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-gray-200 rounded-2xl border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        @if (Auth::user()->role->name === 'admin')
                            <th class="py-2 px-4 border-b">Name</th>
                        @endif
                        <th class="py-2 px-4 border-b">Day</th>
                        <th class="py-2 px-4 border-b">Time In</th>
                        <th class="py-2 px-4 border-b">Time Out</th>
                        @if (Auth::user()->role->name === 'instructor')
                            <th class="py-2 px-4 border-b w-40">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($schedules as $schedule)
                        <tr class="hover:bg-blue-50">
                            @if (Auth::user()->role->name === 'admin')
                                <td class="py-2 px-7 border-b text-center">
                                    {{ $schedule->user->first_name }} {{ $schedule->user->last_name }}
                                </td>
                            @endif
                            <td class="py-2 px-7 border-b text-center">{{ $schedule->day }}</td>
                            <td class="py-2 px-7 border-b text-center">
                                {{ $schedule->time_in ? $schedule->time_in->format('g:i A') : 'N/A' }}
                            </td>
                            <td class="py-2 px-7 border-b text-center">
                                {{ $schedule->time_out ? $schedule->time_out->format('g:i A') : 'N/A' }}
                            </td>
                            @if (Auth::user()->role->name === 'instructor')
                                <td class="py-2 px-7 border-b flex justify-center w-40">
                                    <a href="{{ route('schedules.edit', $schedule->id) }}"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">
                                        Edit
                                    </a>
                                    <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded"
                                            onclick="return confirm('Are you sure you want to delete this schedule?');">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <x-paginator :paginator="$schedules" />
    </div>
</x-layout>
