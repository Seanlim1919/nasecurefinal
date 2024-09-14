<x-layout>
    <x-sidebar />
    <div class="container mx-auto  border p-5 w-1/2 rounded-lg border-blue-600 bg-gray-100">
        <h2 id="month-year" class="mx-4 text-lg font-bold"></h2>

        <div id="calendar" class="mb-8 grid grid-cols-7 gap-1 text-gray-600"></div>

        <div class="flex justify-between mb-4 font-bold">
            <button id="prev-month" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </button>

            <button id="next-month" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </button>
        </div>
    </div>

    <div class="w-full flex justify-between gap-5 mb-4">
        <div class="bg-gray-100 w-1/2 min-h-96 rounded-lg border-blue-600 border mt-4">
            <table class="min-w-full mt-4">
                <thead>
                    <tr>
                        <th class="py-2 text-center">Section Name</th>
                        <th class="py-2 text-center">Course</th>
                    </tr>
                </thead>
                <tbody id="sections-table-body">
                    @foreach ($sections as $section)
                        <tr class="cursor-pointer hover:bg-blue-100" data-section-id="{{ $section->id }}">
                            <td class="py-2 text-center">{{ $section->section_name }}</td>
                            <td class="py-2 text-center">{{ $section->course->course_name ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="bg-gray-100 w-1/2 min-h-96 rounded-lg border-blue-600 border mt-4">
            <table class="min-w-full mt-4">
                <thead>
                    <tr>
                        <th class="py-2 text-center">Student Name</th>
                        <th class="py-2 text-center">Attendance Date</th>
                        <th class="py-2 text-center">Time In</th>
                        <th class="py-2 text-center">Time Out</th>
                    </tr>
                </thead>
                <tbody id="attendance-log"></tbody>
            </table>
        </div>
    </div>

    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.10.4/dayjs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.10.4/plugin/utc.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.10.4/plugin/timezone.min.js"></script> --}}

    <script src="{{ asset('date/dayjs.min.js') }}"></script>
    <script src="{{ asset('date/utc.min.js') }}"></script>
    <script src="{{ asset('date/timezone.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            dayjs.extend(window.dayjs_plugin_utc);
            dayjs.extend(window.dayjs_plugin_timezone);

            const calendar = document.getElementById('calendar');
            const attendanceLogTable = document.getElementById('attendance-log');
            const monthYearDisplay = document.getElementById('month-year');
            const prevMonthButton = document.getElementById('prev-month');
            const nextMonthButton = document.getElementById('next-month');

            let currentMonth = dayjs().month();
            let currentYear = dayjs().year();
            let selectedSectionId = null;

            function renderCalendar() {
                calendar.innerHTML = '';

                const weekDays = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
                weekDays.forEach(day => {
                    const headerCell = document.createElement('div');
                    headerCell.className = 'py-2 text-center font-bold text-blue-600';
                    headerCell.textContent = day;
                    calendar.appendChild(headerCell);
                });

                const daysInMonth = dayjs(`${currentYear}-${currentMonth + 1}-01`).daysInMonth();
                const firstDayOfMonth = dayjs(`${currentYear}-${currentMonth + 1}-01`).day();
                const totalCells = Math.ceil((daysInMonth + firstDayOfMonth) / 7) * 7;

                let todayButton = null;

                for (let i = 0; i < totalCells; i++) {
                    const dateButton = document.createElement('button');
                    const day = i - firstDayOfMonth + 1;
                    if (i >= firstDayOfMonth && day <= daysInMonth) {
                        const date = dayjs(`${currentYear}-${currentMonth + 1}-${day}`).format('YYYY-MM-DD');
                        dateButton.textContent = day;
                        dateButton.className =
                            'px-4 py-2 m-1 text-center rounded hover:bg-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500';
                        dateButton.dataset.date = date;
                        dateButton.addEventListener('click', function() {
                            if (selectedSectionId) {
                                fetchAttendanceLogs(this.dataset.date, selectedSectionId);
                            }
                            setActiveDate(this.dataset.date);
                        });

                        if (date === dayjs().format('YYYY-MM-DD')) {
                            todayButton = dateButton;
                        }
                    } else {
                        dateButton.className = 'px-4 py-2 m-1 text-center rounded text-gray-400';
                    }
                    calendar.appendChild(dateButton);
                }

                if (todayButton) {
                    todayButton.click();
                }

                monthYearDisplay.textContent = dayjs(`${currentYear}-${currentMonth + 1}-01`).format('MMMM YYYY');
            }

            function setActiveDate(date) {
                document.querySelectorAll('#calendar button').forEach(button => {
                    button.classList.remove('bg-blue-700', 'text-white');
                    if (button.dataset.date === date) {
                        button.classList.add('bg-blue-700', 'text-white');
                    }
                });
            }

            function fetchAttendanceLogs(date, sectionId) {
                const sanitizedDate = encodeURIComponent(date);
                const sanitizedSectionId = encodeURIComponent(sectionId);

                fetch(`/attendance-logs?date=${sanitizedDate}&section_id=${sanitizedSectionId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        attendanceLogTable.innerHTML = '';
                        data.forEach(log => {
                            const row = document.createElement('tr');
                            const formattedDate = dayjs(log.attendance_date).format('YYYY-MM-DD');
                            const formattedTimeIn = dayjs(log.time_in).format('HH:mm:ss');
                            const formattedTimeOut = dayjs(log.time_out).format('HH:mm:ss');
                            row.innerHTML = `
                            <td class="py-2 text-center">${log.student.name}</td>
                            <td class="py-2 text-center">${formattedDate}</td>
                            <td class="py-2 text-center">${formattedTimeIn}</td>
                            <td class="py-2 text-center">${formattedTimeOut}</td>
                        `;
                            attendanceLogTable.appendChild(row);
                        });
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        alert('Failed to fetch attendance logs. Please try again later.');
                    });
            }

            prevMonthButton.addEventListener('click', function() {
                const previousMonth = dayjs(`${currentYear}-${currentMonth + 1}-01`).subtract(1, 'month');
                currentMonth = previousMonth.month();
                currentYear = previousMonth.year();
                renderCalendar();
            });

            nextMonthButton.addEventListener('click', function() {
                const nextMonth = dayjs(`${currentYear}-${currentMonth + 1}-01`).add(1, 'month');
                currentMonth = nextMonth.month();
                currentYear = nextMonth.year();
                renderCalendar();
            });

            document.querySelectorAll('tbody tr[data-section-id]').forEach(row => {
                row.addEventListener('click', function() {
                    document.querySelectorAll('tbody tr').forEach(tr => {
                        tr.classList.remove('bg-blue-100', 'font-bold');
                    });
                    this.classList.add('bg-blue-100', 'font-bold');
                    selectedSectionId = this.dataset.sectionId;
                    const selectedDate = document.querySelector('#calendar button.bg-blue-700')
                        ?.dataset.date;
                    if (selectedDate) {
                        fetchAttendanceLogs(selectedDate, selectedSectionId);
                    }
                });
            });

            renderCalendar();
        });
    </script>
</x-layout>
