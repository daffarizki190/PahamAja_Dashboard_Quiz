<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use App\Services\AiGeneratorService;
use App\Services\FileParserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AiQuizController extends Controller
{
    protected $fileParser;

    protected $aiGenerator;

    public function __construct(FileParserService $fileParser, AiGeneratorService $aiGenerator)
    {
        $this->fileParser = $fileParser;
        $this->aiGenerator = $aiGenerator;
    }

    /**
     * Show the form to create a quiz with AI.
     */
    public function aiCreate()
    {
        return view('admin.quizzes.ai-create');
    }

    /**
     * Generate questions from uploaded document and show preview.
     */
    public function aiGenerate(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,docx,pptx|max:10240',
            'content_text' => 'nullable|string|min:100',
            'question_count' => 'required|integer|min:1|max:20',
            'difficulty' => 'required|in:Easy,Medium,Hard',
            'time_limit' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'qc' => 'nullable|boolean',
        ]);

        try {
            $text = '';

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $text = $this->fileParser->parseFile($file->getPathname(), $file->getClientOriginalExtension());
            } elseif ($request->filled('content_text')) {
                $text = (string) $request->input('content_text');
            } else {
                throw new Exception('Please provide either a document or paste some text.');
            }

            if (empty(trim($text))) {
                throw new Exception('Could not extract or read any text from the source.');
            }

            $regenToken = $request->filled('regen_token') ? (string) $request->input('regen_token') : null;
            $questions = $this->aiGenerator->generateQuestions($text, $request->question_count, $request->difficulty, $regenToken);
            $qc = $request->boolean('qc') ? $this->aiGenerator->qualityCheck($questions) : null;

            return view('admin.quizzes.ai-preview', [
                'title' => $request->title,
                'difficulty' => $request->difficulty,
                'time_limit' => $request->time_limit,
                'passing_score' => $request->passing_score,
                'question_count' => (int) $request->question_count,
                'source_text' => mb_substr($text, 0, 20000),
                'qc_enabled' => $request->boolean('qc'),
                'questions' => $questions,
                'qc' => $qc,
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'AI Generation Error: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Store the generated quiz and questions.
     */
    public function aiStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'time_limit' => 'required|integer',
            'passing_score' => 'required|integer|min:0|max:100',
            'questions' => 'required|array',
        ]);

        try {
            $quiz = Quiz::create([
                'title' => $request->title,
                'slug' => Str::slug($request->title).'-'.Str::random(5),
                'time_limit' => $request->time_limit,
                'passing_score' => $request->passing_score,
            ]);

            foreach ($request->questions as $qData) {
                $question = Question::create([
                    'quiz_id' => $quiz->id,
                    'text' => $qData['text'],
                ]);

                foreach ($qData['options'] as $oData) {
                    Option::create([
                        'question_id' => $question->id,
                        'text' => $oData['text'],
                        'is_correct' => isset($oData['is_correct']) && ($oData['is_correct'] == '1' || $oData['is_correct'] === true),
                    ]);
                }
            }

            return redirect()->route('admin.quizzes.index')->with('success', 'AI Quiz created successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Storage Error: '.$e->getMessage());
        }
    }

    public function aiModels()
    {
        $models = $this->aiGenerator->listAvailableModels();

        return view('admin.quizzes.ai-models', compact('models'));
    }
}
