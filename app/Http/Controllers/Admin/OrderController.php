<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\OrderServiceInterface;
use App\DataTransferObjects\OrderDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\IndexRequest;
use App\Http\Resources\OrderCollection;
use App\Models\Order;
use App\Traits\SharedControllers;

class OrderController extends Controller
{
    use SharedControllers;

    public function __construct(
        private OrderServiceInterface $orderService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        $dto = OrderDto::fromRequest($request);

        $data = new OrderCollection(
            $this->orderService->getAllOrdersWithPaginate($dto, $this->perPage(), $this->orderBy())
        );

        return response()->success($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return response()->success($this->orderService->show($order));
    }
}
