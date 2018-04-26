<?php
/**
 * Created by PhpStorm.
 * User: Seungmin Lee
 * Date: 2018-04-26
 * Time: 오후 3:44
 */

namespace App\Http\Controllers;


class ResponseObject {
    // 01. 멤버 변수 선언
    public $status, $message;

    // 02. 생성자 정의
    public function __construct(bool $argStatus, $argMessage)
    {
        $this->status = $argStatus;
        $this->message = $argMessage;
    }
}