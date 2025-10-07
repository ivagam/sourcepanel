<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index()
    {        
        $task = Task::orderBy('created_at', 'desc')->get();

        return view('task.taskList', compact('task'));
    }

    public function create(Request $request)
    {        
        return view('task.addTask');
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'description' => 'required|string|max:1000',
            'status'      => 'required|in:pending,in_progress,completed',
            'priority'    => 'required|in:low,medium,high'            
        ]);

        Task::create([
            'description' => $request->description,
            'status'      => $request->status,
            'priority'    => $request->priority
        ]);

        return redirect()->route('taskList')->with('success', 'Task added successfully.');
    }

    public function edit($id)
    {
        $task = Task::findOrFail($id);

        $statuses = ['pending', 'in_progress', 'completed'];
        $priorities = ['low', 'medium', 'high'];

        return view('task.editTask', compact('task', 'statuses', 'priorities'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:1000',
            'status'      => 'required|in:pending,in_progress,completed',
            'priority'    => 'required|in:low,medium,high',            
        ]);

        Task::where('task_id', $id)->update([
            'description' => $request->description,
            'status'      => $request->status,
            'priority'    => $request->priority,            
        ]);

        return redirect()->route('taskList')->with('success', 'Task updated successfully.');
    }

    public function destroy($id)
    {
        Task::where('task_id', $id)->delete();

        return redirect()->route('taskList')->with('success', 'Task deleted successfully.');
    }

    public function updateStatus(Request $request)
    {
        $task = Task::find($request->id);

        if (!$task) {
            return response()->json(['success' => false, 'message' => 'Task not found']);
        }

        // 🔄 Toggle logic
        if ($task->status === 'completed') {
            $task->status = 'pending';
        } else {
            $task->status = 'completed';
        }

        $task->save();

        return response()->json([
            'success' => true,
            'new_status' => $task->status,
            'message' => 'Status updated to ' . ucfirst($task->status)
        ]);
    }

}