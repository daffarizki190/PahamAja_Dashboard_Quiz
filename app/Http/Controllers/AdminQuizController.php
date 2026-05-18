<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminQuizController extends Controller
{
    /**
     * Update the live status of a public quiz.
     */
    public function updateLiveStatus(Request $request, Quiz $quiz)
    {
        $request->validate([
            'status' => 'required|in:ready,waiting,active,closed'
        ]);

        // Use direct DB update to avoid any Eloquent/PostgreSQL casting issues
        DB::table('quizzes')->where('id', $quiz->id)->update(['status' => $request->status]);

        return response()->json([
            'ok' => true, 
            'status' => $request->status,
            'message' => 'Status updated to ' . $request->status
        ]);
    }

    /**
     * Fetch the list of participants currently in the waiting room for the quiz.
     */
    public function liveParticipants(Quiz $quiz)
    {
        // Release session lock to prevent blocking other requests
        if (session()->isStarted()) {
            session()->save();
        }

        // Group by NIM to avoid showing the same person multiple times if they re-join
        $participants = Participant::where('quiz_id', $quiz->id)
            ->whereNull('score')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'name', 'nim', 'location', 'created_at'])
            ->unique('nim')
            ->values();

        return response()->json([
            'participants' => $participants,
            'count' => $participants->count()
        ]);
    }
}
