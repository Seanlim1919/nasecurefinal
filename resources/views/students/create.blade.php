<x-layout>
    <x-sidebar />
    <div class="container mx-auto w-full p-5 bg-gray-200 rounded-3xl flex">

        <div class="w-full m-5">
            <h1 class="text-xl font-bold mb-4">Add Student</h1>
            @if (session('error'))
                <x-flashMsg msg="{{ session('error') }}" bg="bg-red-500" />
            @endif

            @if (session('success'))
                <x-flashMsg msg="{{ session('success') }}" bg="bg-green-500" />
            @endif

            @if (session('deleted'))
                <x-flashMsg msg="{{ session('deleted') }}" bg="bg-red-500" />
            @endif
            <form action="{{ route('students.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" id="name" name="name"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <p class="text-red-700 text-sm">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="student_id" class="block text-sm font-medium text-gray-700">Student ID</label>
                    <input type="text" id="student_id" name="student_id"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        value="{{ old('student_id') }}" required>
                    @error('student_id')
                        <p class="text-red-700 text-sm">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="text" id="email" name="email"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        value="{{ old('email') }}" required>
                    @error('email')
                        <p class="text-red-700 text-sm">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="rfid" class="block text-sm font-medium text-gray-700">RFID</label>
                    <div class="relative">
                        <input type="text" id="rfid" name="rfid"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            value="{{ old('rfid') }}">
                        <span id="rfid_count" class="absolute right-2 bottom-2 text-sm text-gray-500"></span>
                    </div>
                    @error('rfid')
                        <p class="text-red-700 text-sm">{{ $message }}</p>
                    @enderror
                </div>
                <div>
    <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
    <select id="year" name="year"
        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        <option value="" disabled selected>Select Year</option>
        @for ($i = 1; $i <= 4; $i++)
            <option value="{{ $i }}" {{ old('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
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
        <option value="" disabled selected>Select Section</option>
        @foreach (range('A', 'H') as $section)
            <option value="{{ $section }}" {{ old('section') == $section ? 'selected' : '' }}>{{ $section }}</option>
        @endforeach
    </select>
    @error('section')
        <p class="text-red-700 text-sm">{{ $message }}</p>
    @enderror
</div>

<input type="hidden" id="section_id" name="section_id" value="{{ old('section_id') }}">
                <div>
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Add Student
                    </button>
                </div>
            </form>
        </div>

        <div class="container mx-auto p-5 bg-gray-200 rounded-3xl w-full">
            <h1 class="text-xl font-bold mb-4">Import Students</h1>
            <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="file" class="block text-sm font-medium text-gray-700">Upload Excel File</label>
                    <input type="file" name="file" id="file" class="mt-1 block w-full">
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 w-full">
                        Import
                    </button>
                </div>
            </form>
        </div>

    </div>
    <script src="{{ asset('js/characterCount.js') }}" defer></script>
    <script>
        document.getElementById('year').addEventListener('change', updateSectionId);
        document.getElementById('section').addEventListener('change', updateSectionId);

        function updateSectionId() {
            const year = document.getElementById('year').value;
            const section = document.getElementById('section').value;
            const sectionId = year && section ? year + section : '';
            document.getElementById('section_id').value = sectionId;
        }
    </script>
</x-layout>
