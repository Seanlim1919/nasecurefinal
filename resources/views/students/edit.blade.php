<x-layout>
    <x-sidebar />
    <div class="container mx-auto p-5 bg-gray-200 rounded-3xl">
        <h1 class="text-xl font-bold mb-4">Edit Student</h1>
        <form action="{{ route('students.update', $student->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="name" name="name"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    value="{{ old('name', $student->name) }}" required autofocus>
                @error('name')
                    <p class="text-red-700 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Student ID -->
            <div>
                <label for="student_id" class="block text-sm font-medium text-gray-700">Student ID</label>
                <input type="text" id="student_id" name="student_id"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    value="{{ old('student_id', $student->student_id) }}" required>
                @error('student_id')
                    <p class="text-red-700 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    value="{{ old('email', $student->email) }}" required>
                @error('email')
                    <p class="text-red-700 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- RFID -->
            <div>
                <label for="rfid" class="block text-sm font-medium text-gray-700">RFID</label>
                <div class="relative">
                    <input type="text" id="rfid" name="rfid"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        value="{{ old('rfid', $student->rfid) }}">
                    <span id="rfid_count" class="absolute right-2 bottom-2 text-sm text-gray-500"></span>
                </div>
                @error('rfid')
                    <p class="text-red-700 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <!-- Section -->
            <div>
    <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
    <select id="year" name="year"
        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        <option value="" disabled {{ old('year') ? '' : 'selected' }}>Select Year</option>
        @for ($i = 1; $i <= 4; $i++)
            <option value="{{ $i }}" {{ old('year', substr($student->section_id ?? '', 0, 1)) == $i ? 'selected' : '' }}>{{ $i }}</option>
        @endfor
    </select>
    @error('year')
        <p class="text-red-700 text-sm">{{ $message }}</p>
    @enderror
</div>

<div class="mt-4">
    <label for="section" class="block text-sm font-medium text-gray-700">Section</label>
    <select id="section" name="section"
        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        <option value="" disabled {{ old('section') ? '' : 'selected' }}>Select Section</option>
        @foreach (range('A', 'H') as $section)
            <option value="{{ $section }}" {{ old('section', substr($student->section_id ?? '', 1)) == $section ? 'selected' : '' }}>{{ $section }}</option>
        @endforeach
    </select>
    @error('section')
        <p class="text-red-700 text-sm">{{ $message }}</p>
    @enderror
</div>



            <!-- Submit Button -->
            <div>
                <button type="submit"
                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Update Student
                </button>
            </div>
        </form>
    </div>
    <script src="{{ asset('js/characterCount.js') }}" defer></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const yearElement = document.getElementById('year');
        const sectionElement = document.getElementById('section');

        const form = document.querySelector('form');
        form.addEventListener('submit', function (e) {
            const year = yearElement.value;
            const section = sectionElement.value;

            if (year && section) {
                const sectionIdInput = document.createElement('input');
                sectionIdInput.type = 'hidden';
                sectionIdInput.name = 'section_id';
                sectionIdInput.value = year + section;
                form.appendChild(sectionIdInput);
            }
        });
    });
</script>
</x-layout>
