<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateSectionRequest;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();
    
        $sectionsQuery = Section::with('course', 'creator')
            ->where(function ($query) use ($search) {
                $query->where('section_name', 'like', "%{$search}%")
                    ->orWhere('course_id', 'like', "%{$search}%")
                    ->orWhereHas('course', function ($query) use ($search) {
                        $query->where('course_name', 'like', "%{$search}%");
                    });
            });
    
        if ($user->role->name !== 'admin') {
            $sectionsQuery->where('created_by', $user->id);
        }
    
        $sections = $sectionsQuery->get();  
        foreach ($sections as $section) {
            $studentCount = $section->students()->count(); 
            $section->update(['student_count' => $studentCount]); 
        }
    
        $sections = $sectionsQuery->latest()->paginate(10);
    
        return view('sections.index', ['sections' => $sections]);
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courses = Course::all();
        return view('sections.create', compact('courses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'year' => ['required', 'numeric', 'in:1,2,3,4'],  
            'section' => ['required', 'string', 'in:A,B,C,D,E,F,G,H'],  
            'course_id' => ['nullable', 'exists:courses,id'],  
            'time_in' => ['required', 'date_format:H:i'], 
            'time_out' => ['required', 'date_format:H:i'],  
            'schedule' => ['required', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],  
        ]);
    
        $fields['section_name'] = $request->input('year') . $request->input('section');
    
        $existingSection = Section::where('schedule', $fields['schedule'])
            ->where(function ($query) use ($fields) {
                $query->where(function ($query) use ($fields) {
                    $query->where('time_in', '<', $fields['time_in'])
                          ->where('time_out', '>', $fields['time_in']);
                })->orWhere(function ($query) use ($fields) {
                    $query->where('time_in', '<', $fields['time_out'])
                          ->where('time_out', '>', $fields['time_out']);
                })->orWhere(function ($query) use ($fields) {
                    $query->where('time_in', '>=', $fields['time_in'])
                          ->where('time_out', '<=', $fields['time_out']);
                });
            })->first();
    
        if ($existingSection) {
            return redirect()->back()->withErrors(['schedule' => 'The time conflicts with another schedule. Please choose a different time.']);
        }
    
        $fields['created_by'] = Auth::id();
    
        Section::create($fields);
    
        return redirect()->route('sections.index')->with('success', 'Section added successfully.');
    }
    
    /**
     * Display the specified resource.
     */
    public function show(Section $section)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Section $section)
    {
        $courses = Course::all();  // Assuming you need the courses for the dropdown
        return view('sections.edit', compact('section', 'courses'));
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Section $section)
    {
        // Validate the request
        $fields = $request->validate([
            'year' => ['required', 'numeric', 'in:1,2,3,4'],
            'section' => ['required', 'string', 'in:A,B,C,D,E,F,G,H'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'time_in' => ['required', 'date_format:H:i'],
            'time_out' => ['required', 'date_format:H:i'],
            'schedule' => ['required', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
        ]);
    
        // Combine year and section into section_name
        $fields['section_name'] = $request->input('year') . $request->input('section');
    
        // Check for time conflicts (same logic as in the store method)
        $existingSection = Section::where('schedule', $fields['schedule'])
            ->where(function ($query) use ($fields) {
                $query->where(function ($query) use ($fields) {
                    $query->where('time_in', '<', $fields['time_in'])
                          ->where('time_out', '>', $fields['time_in']);
                })->orWhere(function ($query) use ($fields) {
                    $query->where('time_in', '<', $fields['time_out'])
                          ->where('time_out', '>', $fields['time_out']);
                })->orWhere(function ($query) use ($fields) {
                    $query->where('time_in', '>=', $fields['time_in'])
                          ->where('time_out', '<=', $fields['time_out']);
                });
            })->first();
    
        // If conflict exists, return an error
        if ($existingSection) {
            return redirect()->back()->withErrors(['schedule' => 'The time conflicts with another schedule. Please choose a different time.']);
        }
    
        // Update the section
        $section->update($fields);
    
        // Redirect with success message
        return redirect()->route('sections.index')->with('success', 'Section updated successfully.');
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Section $section)
    {
        $section->delete();

        return back()->with('deleted', 'The section is deleted');
    }
}
