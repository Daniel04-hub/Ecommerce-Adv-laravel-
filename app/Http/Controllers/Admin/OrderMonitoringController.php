<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderMonitoringController extends Controller
{
    public function index()
    {
        $orders = Order::with(['product', 'customer'])->latest()->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }
}
