<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
/**
 *  클래스명:               AlertGenerator
 *  설명:                   알람을 전송하는 이벤트를 정의하는 클래스
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 6월 23일　
 */
/**
 *  クラス名：              AlertGenerator
 *  説明：                  アラムを転送するイベントを定義するクラス
 *  作った人：              3-WDJ 春目指し 1401213 イ・スンミン
 *  作った日：              2018年6月23日　
 */
class AlertGenerator
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    // 멤버 변수 지정
    protected $receivers;       // 수신자 목록(受信者のリスト)
    protected $contents;        // 내용(内容)
    protected $regDate;         // 알람 송신 일자(アラムの送信日)

    /**
     * Create a new event instance.
     *
     * @param array $argReceiver
     * @param $argContents
     * @param $argRegDate
     */
    public function __construct(Array $argReceiver, $argContents, $argRegDate)
    {
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
