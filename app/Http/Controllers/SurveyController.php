<?php

namespace App\Http\Controllers;

use App\Models\SatisfactionSurvey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SurveyController extends Controller
{
    public function show(string $token): View
    {
        $survey = SatisfactionSurvey::where('token', $token)
            ->with(['project.client'])
            ->firstOrFail();

        if (!$survey->isValid()) {
            abort(410, 'Esta encuesta ya no está disponible.');
        }

        return view('survey.show', compact('survey'));
    }

    public function store(Request $request, string $token)
    {
        $survey = SatisfactionSurvey::where('token', $token)->firstOrFail();

        if (!$survey->isValid()) {
            abort(410, 'Esta encuesta ya no está disponible.');
        }

        $validated = $request->validate([
            'nps_score'          => ['required', 'integer', 'min:0', 'max:10'],
            'ces_score'          => ['nullable', 'integer', 'min:1', 'max:7'],
            'csat_score'         => ['nullable', 'integer', 'min:1', 'max:5'],
            'what_went_well'     => ['nullable', 'string', 'max:1000'],
            'what_could_improve' => ['nullable', 'string', 'max:1000'],
            'additional_comments'=> ['nullable', 'string', 'max:1000'],
        ]);

        SurveyResponse::create([
            ...$validated,
            'satisfaction_survey_id' => $survey->id,
            'ip_address'             => $request->ip(),
        ]);

        $survey->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        return view('survey.thank-you', compact('survey'));
    }
}
