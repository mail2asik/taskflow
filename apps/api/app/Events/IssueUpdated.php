<?php

namespace App\Events;

use App\Http\Resources\V1\IssueResource;
use App\Models\Issue;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IssueUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $issue;
    public int $projectId;

    public function __construct(Issue $issue)
    {
        $this->issue = (new IssueResource($issue->load(['assignee', 'reporter', 'labels'])))->resolve();
        $this->projectId = $issue->project_id;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("projects.{$this->projectId}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'issue.updated';
    }
}