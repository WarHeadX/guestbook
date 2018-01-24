<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewAnswer implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
    * @var User
    */
    public $user;

    /**
     * @var Message
     */
    public $message;

    /**
     * @var Message
     */
    public $answer;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $message, $answer)
    {
        $this->user = $user;
        $this->message = $message;
        $this->answer = $answer;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['answer.' . $this->user->id];
    }

    public function broadcastAs()
    {
        return 'new-answer';
    }
    
    public function broadcastWith()
    {
        return [
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'message' => [
                'id' => $this->message->id,
                'message' => $this->message->message,
            ],
            'answer' => $this->answer->message,
        ];
    }
    
}
