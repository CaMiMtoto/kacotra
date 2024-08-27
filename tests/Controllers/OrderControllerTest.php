<?php

namespace Tests\Http\Controllers;

use App\Http\Controllers\OrderController;
use PHPUnit\Framework\TestCase;

class OrderControllerTest extends TestCase
{

    public function testPayDueOrder()
    {
        $orderController = new OrderController();
        $request = new \App\Http\Requests\ValidatePayDueOrderRequest();
        $request->merge([
            'id' => 1,
            'pay' => 100,
            'payment_type' => 'cash',
            'comment' => 'test comment',
        ]);
        $this->assertNull($orderController->payDueOrder($request));

    }
}
