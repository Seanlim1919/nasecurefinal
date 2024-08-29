<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();

        $sectionsQuery = Section::with(['students', 'course'])
            ->where(function ($query) use ($search) {
                $query->where('section_name', 'like', "%{$search}%")
                    ->orWhereHas('students', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('course', function ($query) use ($search) {
                        $query->where('course_name', 'like', "%{$search}%");
                    });
            });

        if ($user->role->name !== 'admin') {
            $sectionsQuery->where('created_by', $user->id);
        }

        $sections = $sectionsQuery->latest()->paginate(10);

        return view('dashboard.index', ['sections' => $sections]);
    }



    public function fetchAttendanceLogs(Request $request)
    {
        $date = $request->query('date');
        $sectionId = $request->query('section_id');
        $userId = Auth::id();

        $attendanceLogs = AttendanceLog::with('student')
            ->whereDate('attendance_date', $date)
            ->whereHas('student', function ($query) use ($sectionId, $userId) {
                $query->where('section_id', $sectionId)
                    ->where('created_by', $userId);
            })
            ->get();

        return response()->json($attendanceLogs);
    }
}
