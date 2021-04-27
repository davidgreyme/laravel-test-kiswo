<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProductController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Display a listing of the products
     * @param Product $model
     * @return Factory|View
     */
    public function index(Product $model)
    {
        return view('product.index', ['products' => $model->all()]);
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function product_list(Request $request)
    {
        $order_type = $request->get('order_type');
        if (empty($order_type))
        {
            $order_type = 'asc';
        }
        if ($order_type == 'asc')
            $products = Product::orderBy('price')->limit(5)->get();
        else
            $products = Product::orderByDesc('price')->limit(5)->get();
        return view('product.list', ['products' => $products]);
    }

    /**
     * Show the form for creating a new product
     * @return Factory|View
     */
    public function create()
    {
        return view('product.create');
    }

    /**
     * Store a newly created product in storage
     * @param ProductRequest $request
     * @param Product $model
     * @return
     */
    public function store(ProductRequest $request, Product $model)
    {
        $request['user_id'] = auth()->user()->id;
        $model->create($request);
        return redirect()->route('product.index')->withStatus(__('User successfully created.'));
    }

    /**
     * Show the form for editing the specified product
     * @param Product $product
     * @return Factory|View
     */
    public function edit(Product $product)
    {
        if ($product->user_id != auth()->user()->id) {
            abort(403);
        }
        return view('product.edit', ['product' => $product]);
    }

    /**
     * Update the specified product in storage
     * @param ProductRequest $request
     * @param Product $product
     * @return
     */
    public function update(ProductRequest $request, Product $product)
    {
        if ($product->user() != auth()->user()) {
            abort(403);
        }
        $product->update($request);
        return redirect()->route('product.index');
    }

    /**
     * Remove the specified product from storage
     * @param Product $product
     * @return
     */
    public function destroy(Product $product)
    {
        if ($product->user() != auth()->user()) {
            abort(403);
        }
        $product->delete();
        return redirect()->route('product.index')->withStatus(__('Product successfully deleted.'));
    }
}
