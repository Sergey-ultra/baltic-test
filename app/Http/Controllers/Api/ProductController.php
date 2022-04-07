<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function store(ProductRequest $request)
    {
        $res = Product::create($request->all());
        return response()->json([ 'data' => $res ], 201);
    }

    public function show(int $id)
    {

        $product = Product::select('id','name', 'article', 'status', 'data')
            ->where('id', $id)
            ->first();

        if (null === $product) {
            return response()->json(['status' => 'fails', 'data' => null]);
        }
        return response()->json(['status' => 'success', 'data' => $product]);
    }



    public function update(ProductUpdateRequest $request)
    {
        $product = Product::where('id', (int) $request->id)->first();
        if (null === $product) {
            return response()->json(['status' => 'fails', 'data' => null]);
        }
        $product->update($request->all());
        return response()->json(['status' => 'success', 'data' => $product]);
    }


    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['status' => 'success']);
    }
}
