<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todolist;
use Carbon\Carbon;

class TodolistController extends Controller
{
    public function index()
    {
        $todolists = Todolist::orderBy('completed')->get();

        foreach ($todolists as $todolist) {
            $todolist->overdue = Carbon::now()->greaterThan($todolist->due_date);
        }

        return view('home', compact('todolists'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'content' => 'required',
            'deadline' => 'nullable|date', // Add validation for the deadline
        ]);
    
        Todolist::create($data);
    
        return back();
    }

    public function complete(Todolist $todolist)
    {
        $todolist->update(['completed' => true]);
        return back();
    }

    public function destroy(Todolist $todolist)
    {
        $todolist->delete();
        return back();
    }

    // Other methods...

    public function update(Request $request, Todolist $todolist)
    {
        $data = $request->validate([
            'content' => 'required',
            'due_date' => 'date', // Assuming due_date is a date field in your database
        ]);

        $todolist->update($data);

        // Check if the task is overdue and update the 'overdue' status
        $todolist->overdue = Carbon::now()->greaterThan($todolist->due_date);
        $todolist->save();

        return redirect()->route('index');
    }
}
