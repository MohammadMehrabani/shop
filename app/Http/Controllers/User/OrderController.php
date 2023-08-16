<?php

namespace App\Http\Controllers\User;

use App\Contracts\OrderServiceInterface;
use App\DataTransferObjects\OrderDto;
use App\DataTransferObjects\OrderProductDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\IndexRequest;
use App\Http\Requests\Order\StoreRequest;
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
        $request->merge(['userId' => auth()->id()]);
        $dto = OrderDto::fromArray($request->all());

        $data = new OrderCollection(
            $this->orderService->getAllOrdersWithPaginate($dto, $this->perPage(), $this->orderBy())
        );

        return response()->success($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $orderProductDto = [];

        $items = $request->all()['items'];
        foreach ($items as $item) {
            $orderProductDto[] = OrderProductDto::fromArray($item);
        }

        $userId = auth()->id();
        $orderDto = OrderDto::fromArray(['userId' => $userId]);

        $data = $this->orderService->store($orderDto, $orderProductDto, $userId);

        if ($data)
            return response()->success($data);
        else
            return response()->error('not successfully create');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return response()->success($this->orderService->show($order));
    }
}
