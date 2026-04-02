<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\SatisfactionSurvey;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class SendSatisfactionSurveyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Project $project) {}

    public function handle(): void
    {
        // Only send for closed projects with a client
        if ($this->project->status !== 'completed' || !$this->project->client_id) {
            return;
        }

        // Don't duplicate surveys
        $exists = SatisfactionSurvey::where('project_id', $this->project->id)->exists();
        if ($exists) {
            return;
        }

        $survey = SatisfactionSurvey::create([
            'project_id' => $this->project->id,
            'client_id'  => $this->project->client_id,
            'token'      => Str::random(48),
            'status'     => 'pending',
            'sent_at'    => now(),
            'expires_at' => now()->addDays(30),
        ]);

        // TODO: Send email notification to client contact with portal survey URL
        // Mail::to($this->project->client->email)->send(new SurveyInvitation($survey));
        \Illuminate\Support\Facades\Log::info("Survey created for project #{$this->project->id}", [
            'survey_token' => $survey->token,
            'survey_url'   => url("/survey/{$survey->token}"),
        ]);
    }
}
