<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AlertGenerator
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    // 멤버 변수 지정
    protected $receivers;       // 수신자 목록
    protected $contents;        // 내용
    protected $regDate;         // 알람 송신 일자

    /**
     * Create a new event instance.
     *
     * @param array $argReceiver
     * @param $argContents
     * @param $argRegDate
     */
    public function __construct(Array $argReceiver, $argContents, $argRegDate)
    {
        // 멤버변수에 매개 데이터 등록
        $this->receivers    = $argReceiver;
        $this->contents     = $argContents;
        $this->regDate      = $argRegDate;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
