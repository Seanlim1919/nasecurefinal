<x-layout>
    <x-sidebar />
    <div class="container mx-auto p-5 bg-gray-100 rounded-3xl flex flex-col items-center ">
        <div class="w-full max-w-2xl">
            <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Student Details</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="p-4 rounded-lg border border-gray-200">
                        <p class="text-sm text-gray-600 font-semibold">Name</p>
                        <p class="text-lg font-medium text-gray-800">{{ $student->name }}</p>
                    </div>
                    <div class="p-4 rounded-lg border border-gray-200">
                        <p class="text-sm text-gray-600 font-semibold">Student ID</p>
                        <p class="text-lg font-medium text-gray-800">{{ $student->student_id }}</p>
                    </div>
                    <div class="p-4 rounded-lg border border-gray-200">
                        <p class="text-sm text-gray-600 font-semibold">Email</p>
                        <p class="text-lg font-medium text-gray-800 break-all">{{ $student->email }}</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="p-4 rounded-lg border border-gray-200">
                        <p class="text-sm text-gray-600 font-semibold">RFID</p>
                        <p class="text-lg font-medium text-gray-800">{{ $student->rfid ?? 'Not Available' }}</p>
                    </div>
                    <div class="p-4 rounded-lg border border-gray-200">
                        <p class="text-sm text-gray-600 font-semibold">Section</p>
                        <p class="text-lg font-medium text-gray-800">
                            {{ $student->section_id ?? 'Not Assigned' }}
                        </p>
                    </div>

                </div>
            </div>

            <!-- Attendance Logs Table -->
            <div class="w-full mt-10">
                <h2 class="text-2xl font-bold mb-4 text-gray-800">Attendance Logs</h2>
                <div class="overflow-x-auto bg-white rounded-lg shadow-md">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <tr>
                                <th class="py-3 px-6 text-left">Date</th>
                                <th class="py-3 px-6 text-left">Time In</th>
                                <th class="py-3 px-6 text-left">Time Out</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @forelse ($attendanceLogs as $log)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left whitespace-nowrap">
                                        {{ $log->attendance_date->format('Y-m-d') }}</td>
                                    <td class="py-3 px-6 text-left">
                                        {{ optional($log->time_in)->format('H:i:s') ?? 'N/A' }}
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        {{ optional($log->time_out)->format('H:i:s') ?? 'N/A' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-3 px-6 text-center">No attendance logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Links -->
                <x-paginator :paginator="$attendanceLogs" />

            </div>

            <div class="mt-8 text-center">
                <a href="{{ route('students.index') }}"
                    class="inline-flex items-center px-6 py-3 border border-transparent rounded-full shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </div>
</x-layout>
