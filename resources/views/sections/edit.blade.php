<x-layout>
    <x-sidebar />
    <div class="container mx-auto p-5 bg-gray-200 rounded-3xl">
        <h1 class="text-xl font-bold mb-4">Edit Section</h1>
        <form action="{{ route('sections.update', $section->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Split section_name into year and section -->
            @php
                $year = substr($section->section_name, 0, 1);  
                $sectionLetter = substr($section->section_name, 1); 
            @endphp

            <!-- Year Dropdown -->
            <div>
                <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                <select id="year" name="year"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="" disabled>Select Year</option>
                    @for ($i = 1; $i <= 4; $i++)
                        <option value="{{ $i }}" {{ old('year', $year) == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
                @error('year')
                    <p class="text-red-700 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Section Dropdown -->
            <div class="mt-4">
                <label for="section" class="block text-sm font-medium text-gray-700">Section</label>
                <select id="section" name="section"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="" disabled>Select Section</option>
                    @foreach (range('A', 'H') as $sectionChar)
                        <option value="{{ $sectionChar }}" {{ old('section', $sectionLetter) == $sectionChar ? 'selected' : '' }}>{{ $sectionChar }}</option>
                    @endforeach
                </select>
                @error('section')
                    <p class="text-red-700 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Course Dropdown -->
            <div>
                <label for="course_id" class="block text-sm font-medium text-gray-700">Course</label>
                <select id="course_id" name="course_id"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Select Course</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}"
                            {{ old('course_id', $section->course_id ?? '') == $course->id ? 'selected' : '' }}>
                            {{ $course->course_name }}
                        </option>
                    @endforeach
                </select>
                @error('course_id')
                    <p class="text-red-700 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Time In -->
            <div>
                <label for="time_in" class="block text-sm font-medium text-gray-700">Time In</label>
                <input type="time" id="time_in" name="time_in"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    value="{{ old('time_in', $section->time_in) }}">
                @error('time_in')
                    <p class="text-red-700 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Time Out -->
            <div>
                <label for="time_out" class="block text-sm font-medium text-gray-700">Time Out</label>
                <input type="time" id="time_out" name="time_out"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    value="{{ old('time_out', $section->time_out) }}">
                @error('time_out')
                    <p class="text-red-700 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Schedule Dropdown -->
            <div>
                <label for="schedule" class="block text-sm font-medium text-gray-700">Schedule</label>
                <select id="schedule" name="schedule"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Select Schedule</option>
                    @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                        <option value="{{ $day }}" {{ old('schedule', $section->schedule) == $day ? 'selected' : '' }}>
                            {{ ucfirst($day) }}
                        </option>
                    @endforeach
                </select>
                @error('schedule')
                    <p class="text-red-700 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit"
                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Update Section
                </button>
            </div>
        </form>
    </div>
</x-layout>
