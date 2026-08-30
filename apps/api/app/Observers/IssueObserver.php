<?php

namespace App\Observers;

use App\Models\Issue;
use App\Models\IssueActivity;
use Illuminate\Support\Facades\Auth;

class IssueObserver
{
    public function created(Issue $issue): void
    {
        $userId = Auth::id() ?? $issue->reporter_id;

        IssueActivity::create([
            'issue_id' => $issue->id,
            'user_id' => $userId,
            'action' => 'created',
            'description' => "created issue {$issue->issue_key}",
        ]);
    }

    public function updated(Issue $issue): void
    {
        $userId = Auth::id() ?? $issue->reporter_id;

        if ($issue->isDirty('status')) {
            IssueActivity::create([
                'issue_id' => $issue->id,
                'user_id' => $userId,
                'action' => 'status_changed',
                'description' => "changed status from {$issue->getOriginal('status')->value} to {$issue->status->value}",
            ]);
        }

        if ($issue->isDirty('assignee_id')) {
            $assigneeName = $issue->assignee?->name ?? 'Unassigned';
            IssueActivity::create([
                'issue_id' => $issue->id,
                'user_id' => $userId,
                'action' => 'assigned',
                'description' => "assigned issue to {$assigneeName}",
            ]);
        }
    }
}