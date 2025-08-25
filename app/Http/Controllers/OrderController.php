<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\OrderStatus;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class OrderController extends Controller
{
    public $statusService;

    public function __construct(OrderStatusService $statusService) {
        $this->statusService = $statusService;
    }

    public function index(Request $request){
        $query = Order::query()->where('user_id', $request->user()->id);

        if ($status = $request->query('status')) {
            $query->where('order_status', $status);
        }
        if ($from = $request->query('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $orders = $query->latest()->paginate(10);

        return OrderResource::collection($orders);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'order_items'                   => ['required','array','min:1'],
            'order_items.*.product_name'    => ['required','string','max:255'],
            'order_items.*.quantity'        => ['required','integer','min:1'],
            'order_items.*.price'           => ['required','numeric','min:0'],
            'total_amount'                  => ['required','numeric','min:0']
        ]);

        $this->amountValidation($data['order_items'], $data['total_amount']);

        $order = Order::create([
            'user_id'       => $request->user()->id,
            'customer_name' => $request->user()->name,
            'order_items'   => $data['order_items'],
            'order_status'  => OrderStatus::Pending,
            'total_amount'  => $data['total_amount']
        ]);

        return (new OrderResource($order))->response()->setStatusCode(201);
    }

    public function show(Request $request, Order $order)
    {
        $this->authorizeOwner($request, $order);
        
        return new OrderResource($order);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'order_items'                   => ['required','array','min:1'],
            'order_items.*.product_name'    => ['required','string','max:255'],
            'order_items.*.quantity'        => ['required','integer','min:1'],
            'order_items.*.price'           => ['required','numeric','min:0'],
            'total_amount'                  => ['required','numeric','min:0']
        ]);

        if($order->order_status == OrderStatus::Completed)       
            abort(response()->json([
                'message' => 'Completed orders can not be updated.'
            ], 422));

        $this->amountValidation($data['order_items'], $data['total_amount']);

        $order->update([
            'user_id'       => $request->user()->id,
            'customer_name' => $request->user()->name,
            'order_items'   => $data['order_items'],
            'total_amount'  => $data['total_amount']
        ]);

        return (new OrderResource($order))->response()->setStatusCode(201);
    }

    public function destroy(Request $request, Order $order)
    {
        $this->authorizeOwner($request, $order);

        if($order->order_status == OrderStatus::Completed)       
            abort(response()->json([
                'message' => 'Completed orders can not be deleted.'
            ], 422));

        $order->delete();

        return response()->json([
            'message' => 'Order has been deleted.'
        ]);
    }

    public function updateOrderStatus(Request $request, Order $order){
        $this->authorizeOwner($request, $order);

        $data = $request->validate([
            'status' => ['required', new Enum(OrderStatus::class)],
        ]);

        $newStatus = OrderStatus::from($data['status']);

        $this->statusService->statusValidation($order, $newStatus);

        $order->order_status = $data['status'];
        $order->save();

        return new OrderResource($order);
    }

    private function amountValidation($orderItems, $totalAmount)
    {
        if ($orderItems || $totalAmount) {
            $sum = collect($orderItems)->sum(function($i){
                return $i['quantity'] * $i['price'];
            });

            $sum = round($sum, 2);

            $incomingTotal = $totalAmount ? round($totalAmount, 2) : $sum;

            if ($incomingTotal !== $sum) {
                abort(response()->json([
                    'message' => 'Total amount does not match.'
                ], 422));
            }

            return true;
        }
    }

    private function authorizeOwner(Request $request, Order $order): void
    {
        abort_unless($order->user_id == $request->user()->id, 404);
    }
}
