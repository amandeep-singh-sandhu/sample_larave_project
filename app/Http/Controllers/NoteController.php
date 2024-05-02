<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Redis::set('name', 'John');
        // Redis::set('name2', 'Vikas');
        // dd(Redis::get('name2'));
        // set default timezone to IST (Asia/Kolkata) - comment out if you want to use system default timezone
        // date_default_timezone_set('Asia/Kolkata');

        // get current timezone
        date_default_timezone_set(date_default_timezone_get());

        // get current time
        $time = date('h:i:s A');
        
        // get current date in format "YYYY-MM-DD"
        $date = date('Y-m-d');  // current date in "YYYY-MM-DD" format - used for filtering notes by date

        // save signed in user's information in redis server
        $redis = Redis::connection();
        $redis->set('user_'.request()->user()->id, request()->user()->name);
        $redis->set('email_'.request()->user()->id, request()->user()->email);
        $redis->set('login_time_'.request()->user()->id, $time . ' - ' . $date);
        $notes = Note::query()
            ->where('user_id', request()->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate();
        // dd($notes);
        return view('note.index', ['notes' => $notes]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('note.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'note' => ['required', 'string'],
        ]);

        $data['user_id'] = request()->user()->id;
        // $data['user_id'] = 1;
        $note = Note::create($data);

        return to_route('note.show', $note)->with('message', 'Note created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        if ($note->user_id !== request()->user()->id) {
            abort(403);
        }
        return view('note.show', ['note' => $note]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note)
    {
        if ($note->user_id !== request()->user()->id) {
            abort(403);
        }
        return view('note.edit', ['note' => $note]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
        if ($note->user_id !== request()->user()->id) {
            abort(403);
        }

        $data = $request->validate([
            'note' => ['required', 'string'],
        ]);

        $note->update($data);

        return to_route('note.show', $note)->with('message', 'Note updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        if ($note->user_id !== request()->user()->id) {
            abort(403);
        }

        $note->delete();

        return to_route('note.index')->with('message', 'Note deleted successfully');
    }
}
