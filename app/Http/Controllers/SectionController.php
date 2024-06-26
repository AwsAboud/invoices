<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateSectionRequest;

class SectionController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:عرض-قسم', ['only' => ['index']]);
        $this->middleware('permission:اضافة-قسم', ['only' => ['store']]);
        $this->middleware('permission:تعديل-قسم', ['only' => ['update']]);
        $this->middleware('permission:حذف-قسم', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('sections.index',[
            'sections' => Section::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSectionRequest $request)
    {
        Section::create([
        'section_name' => $request->section_name,
        'description' => $request->description,
        'created_by' => auth()->user()->name,
        ]);
        //session()->flash('Add', 'تم اضافة القسم بنجاح ');
        return redirect()->back()->with(['add' => 'تم اضافة القسم بنجاح ']);
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSectionRequest $request)
    {
        $section = Section::findOrFail($request->id);
        $section->update([
            'section_name' => $request->section_name,
            'description' => $request->description
        ]);
        return redirect()->back()->with(['update' => 'تم تعديل القسم بنجاح']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        Section::findOrFail($request->id)->delete();
        return redirect()->back()->with(['delete' => 'تم حذف القسم بنجاح']);

    }
}
