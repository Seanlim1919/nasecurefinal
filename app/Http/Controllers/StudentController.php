<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Section;
use App\Rules\EmailDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Rap2hpoutre\FastExcel\FastExcel; 
class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'asc');
        
        $studentsQuery = Student::query();
    
        if ($user->role->name !== 'admin') {
            $sectionsHandled = Section::where('created_by', $user->id)->pluck('section_name');
    
            $studentsQuery->whereIn('section_id', $sectionsHandled);
        }

        if ($search) {
            $studentsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('student_id', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('rfid', 'like', "%{$search}%")
                    ->orWhere('section_id', 'like', "%{$search}%");
            });
        }

        $sections = Section::all();
        foreach ($sections as $section) {
            $studentCount = $section->students()->count();  
            $section->update(['student_count' => $studentCount]);  
        }
    
        $students = $studentsQuery->orderBy($sort, $direction)
            ->paginate(10);
    
        return view('students.index', ['students' => $students]);
    }
    public function show(Student $student)
{
    $sections = Section::all();
    $attendanceLogs = $student->attendanceLogs()->latest()->paginate(10);

    return view('students.show', [
        'student' => $student,
        'sections' => $sections,
        'attendanceLogs' => $attendanceLogs,
    ]);
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        if ($user->role->name !== 'admin') {
            $sections = Section::where('created_by', $user->id)->get();
        } else {
            $sections = Section::all();
        }

        return view('students.create', compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'student_id' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', new EmailDomain],
            'rfid' => ['nullable', 'string', 'max:50'],
            'year' => ['required', 'numeric', 'in:1,2,3,4'], 
            'section' => ['required', 'string', 'in:A,B,C,D,E,F,G,H'], 
        ]);
    
        $existingStudent = Student::where('email', $fields['email'])
            ->orWhere('student_id', $fields['student_id'])
            ->first();
    
        if ($existingStudent) {
            return back()->withErrors(['email' => 'A student with same Email or student ID already exists.'])->withInput();
        }
    
        $fields['section_id'] = $request->input('year') . $request->input('section');
        $fields['created_by'] = Auth::id();
    
        $student = Student::create($fields);
    
        if ($student->section_id) {
            $studentCount = Student::where('section_id', $student->section_id)->count();
            Section::where('section_name', $student->section_id)->update(['student_count' => $studentCount]);
        }
    
        return redirect()->route('students.index')->with('success', 'Student added successfully.');
    }
    

    public function edit(Student $student)
{
    $user = Auth::user();

    if ($user->role->name !== 'admin') {
        $sections = Section::where('created_by', $user->id)->get();
    } else {
        $sections = Section::all();
    }

    return view('students.edit', compact('student', 'sections'));
}


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $fields = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'student_id' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', new EmailDomain],
            'rfid' => ['nullable', 'string', 'max:50'],
            'year' => ['required', 'numeric', 'in:1,2,3,4'], 
            'section' => ['required', 'string', 'in:A,B,C,D,E,F,G,H'],
        ]);
    
        $existingStudent = Student::where(function($query) use ($fields, $student) {
            $query->where('email', $fields['email'])
                ->orWhere('student_id', $fields['student_id']);
        })->where('id', '!=', $student->id)
        ->first();
    
        if ($existingStudent) {
            return back()->withErrors(['email' => 'A student with same Email or student ID already exists.'])->withInput();
        }
    
        $fields['section_id'] = $request->input('year') . $request->input('section');
    
        $student->update($fields);
    
        if ($student->section_id) {
            $studentCount = Student::where('section_id', $student->section_id)->count();
            Section::where('section_name', $student->section_id)->update(['student_count' => $studentCount]);
        }
    
        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }
    
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $section = $student->section;

        $student->delete();

        if ($section) {
            $section->student_count = $section->students()->count();
            $section->save();
        }

        return back()->with('deleted', 'The student is deleted');
    }




    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx',
        ]);
    
        try {
            $file = $request->file('file');
            $errors = [];
            $sectionStudentCounts = [];  // To track student counts for sections
            $emailRule = new EmailDomain();
    
            (new FastExcel)->import($file, function ($line) use (&$sectionStudentCounts, &$errors, $emailRule) {
    
                if (empty($line['Section ID'])) {
                    $errors[] = "A student is missing their Section ID.";
                    return;
                }
    
                if (empty($line['Name'])) {
                    $errors[] = "A student is missing their Name.";
                    return;
                }
    
                if (empty($line['Email'])) {
                    $errors[] = "A student is missing their Email address.";
                    return;
                }
    
                // Check if either email or student_id already exists
                $existingStudent = Student::where('email', $line['Email'])
                    ->orWhere('student_id', $line['Student ID'])
                    ->first();
    
                if ($existingStudent) {
                    $errors[] = "A student with email {$line['Email']} or student ID {$line['Student ID']} already exists.";
                    return;
                }
    
                $emailError = null;
                $emailRule->validate('email', $line['Email'], function ($error) use (&$emailError) {
                    $emailError = $error;
                });
    
                if ($emailError) {
                    $errors[] = "One of the students has an invalid email address: {$line['Email']}.";
                    return;
                }
    
                // Create or update student without relying on the sections table
                $student = Student::updateOrCreate(
                    ['student_id' => $line['Student ID']],
                    [
                        'name' => $line['Name'],
                        'email' => $line['Email'],
                        'rfid' => $line['RFID'] ?? null,
                        'section_id' => $line['Section ID'],  // Directly assign the section_id
                        'created_by' => Auth::id(),
                    ]
                );
    
                // Track the section_id to update its student count later
                $sectionId = $line['Section ID'];
                $sectionStudentCounts[$sectionId] = ($sectionStudentCounts[$sectionId] ?? 0) + 1;
            });
    
            if (!empty($errors)) {
                $errorMessage = "There were some issues with the import:\n" . implode("\n", $errors);
                Log::error($errorMessage);
                return redirect()->back()->with('error', 'Some students could not be imported. Please check the file and try again.');
            }
    
            // Update the student count for each section after import
            foreach ($sectionStudentCounts as $sectionId => $count) {
                // Update the student count for the section_id (stored in section_name)
                Section::where('section_name', $sectionId)
                    ->update(['student_count' => Student::where('section_id', $sectionId)->count()]);
            }
    
            return redirect()->route('students.index')->with('success', 'Students were imported successfully.');
        } catch (\Exception $e) {
            Log::error('Error importing students: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an issue importing the students. Please try again later.');
        }
    }
    
    
    
}
