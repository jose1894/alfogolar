<?php

namespace App\Http\Controllers\Admin;

use App\AssignProductAttribute;
use App\Http\Controllers\Controller;
use App\Product;
use App\ProductStock;
use App\StockLog;
use Illuminate\Http\Request;

class ProductStockController extends Controller
{
    public function stockCreate($product_id)
    {
        $product                = Product::find($product_id);
        if($product->track_inventory == 0){
            $notify[] = ['error', 'El seguimiento de inventario está deshabilitado para este producto'];
            return redirect()->back()->withNotify($notify);
        }
        $page_title             = 'Administrar inventario';

        $assigned_attributes    = AssignProductAttribute::where('product_id', $product_id)->with(['productAttribute'])->get()->groupBy('product_attribute_id');

        foreach($assigned_attributes as $attributes){
            foreach ($attributes as $attribute){
                $attr_array[] =  $attribute->id.'-'.$attribute->productAttribute->name_for_user. '-' . $attribute->name;
            }
            $attr_data[] = $attr_array;
            unset($attr_array);
        }
        if(isset($attr_data)){
            $combinations =  combinations($attr_data);
        }else{
            $combinations = [];
        }

        $data = [];

        foreach($combinations as $key=>$combination){
            unset($attr_id);
            $result = '';
            $temp_result = [];
            foreach($combination as $attribute){
                $temp       = [];
                $exp        = explode('-',$attribute);
                $result    .= $exp[1].' : ' . $exp[2];
                $attr_id[]  = $exp[0];

                if(end($combination) != $attribute){
                    $result .= ' - ';
                }

                $attr_val = json_encode($attr_id);
            }

            $stock = getStockData($product->id, $attr_val);
            $data[$key]['combination']  = $result;
            $data[$key]['attributes']   = $attr_val;
            $data[$key]['sku']          = $stock['sku']??null;
            $data[$key]['quantity']     = $stock['quantity']??0;
            $data[$key]['stock_id']     = $stock['id']??0;
        }

        return view('admin.products.stock.create', compact('page_title', 'data', 'product'));
    }

    public function stockAdd(Request $request, $id)
    {
        $request->validate([
            'attr'          =>'sometimes|required|string',
            'quantity'      =>'required|numeric|min:0',
            //'sku'           =>'sometimes|string|max:100',
            'type'          =>'required|numeric|between:1,2',
        ]);

        $attributes = $request->attr=='null'?null: $request->attr;

        if($attributes){
            $attributes = json_decode($attributes);
            sort($attributes);
            $attributes = json_encode($attributes);
        }

        if ($request->type == 1) {
            $qty = $request->quantity;
        }else{
            $qty = -$request->quantity;
        }

        $stock = ProductStock::where('product_id', $id)->where('attributes', $attributes)->first();

        if($stock){

            //check sku in product table
            //$check_sku = Product::where('sku', $request->sku)->where('id', '!=', $id)->first();

            // if($check_sku){
            //     $notify[]=['error','Este SKU ya se tomó'];
            //     return back()->withNotify($notify);
            // }else{
            //     $check_sku = ProductStock::where('product_id', '!=' ,$id)->where('attributes', '!=' ,$attributes)->where('sku', $request->sku)->first();
            //     if($check_sku){
            //         $notify[]=['error','Este SKU ya se tomó'];
            //         return back()->withNotify($notify);
            //     }else{
            //         $stock->product_id = $id;
            //         $stock->attributes = $attributes;
            //         $stock->sku        = $request->sku;
            //         $stock->quantity   += $qty;
            //         $stock->save();
            //     }
            // }

                    $stock->product_id = $id;
                    $stock->attributes = $attributes;
                    $stock->quantity   += $qty;
                    $stock->save();

        }else{
            if(isset($request->sku)){
                //check sku
                $check_sku = Product::where('sku', $request->sku)->where('id', '!=', $id)->with('stocks')->orWhereHas('stocks', function($q)use($request){
                    $q->where('sku', $request->sku);
                })->first();

                if($check_sku){
                    $notify[] = ['error', 'Este SKU ya se tomó'];
                    return redirect()->back()->withNotify($notify);
                }
            }
            

            $stock = new ProductStock();
            $stock->product_id = $id;
            $stock->attributes = $attributes;
            $stock->sku        = isset($request->sku) ? $request->sku : '';
            $stock->quantity   = $request->quantity;
            $stock->save();
        }


        if($qty > 0){

            $log = new StockLog;
            $log->stock_id  = $stock->id;
            $log->quantity  = $qty;
            $log->type      = 1;
            $log->save();
        }

        $notify[] = ['success', 'Inventario del Producto Actualizado Exitosamente'];
        return redirect()->back()->withNotify($notify);
    }

    public function stockLog($id)
    {

        $empty_message  = 'El registro de existencias está vacío';
        $product_stock  = ProductStock::find($id);
        $page_title     = "Registros de existencias para SKU:" .@$product_stock->sku;

        if($product_stock){
            $stock_logs     = $product_stock->stockLogs()->paginate(getPaginate());
        }else{
            $notify[] = ['error', 'Aún no se ha creado ningún inventario'];
            return redirect()->back()->withNotify($notify);
        }
        return view('admin.products.stock.log', compact('page_title', 'empty_message', 'product_stock' , 'stock_logs'));
    }

    public function stocks()
    {
        $page_title     = 'Artículos en stock';
        $empty_message  = 'El stock está vacío';
        $stock_data     = ProductStock::where('quantity', '>' , 0)->with('product', function($q){
            return $q->whereHas('categories')->whereHas('brand');
        })->paginate(getPaginate());
        return view('admin.products.items_in_stock', compact('stock_data', 'page_title', 'empty_message'));
    }

    public function stocksLow()
    {
        $page_title     = 'Cantidad de existencias bajas';
        $empty_message  = 'Ningún producto disponible aquí';
        $stock_data     = ProductStock::where('quantity', '<=' , 5)->where('quantity','!=',0)->with('product', function($q){
            return $q->whereHas('categories')->whereHas('brand');
        })->paginate(getPaginate());
        return view('admin.products.items_in_stock', compact('stock_data', 'page_title', 'empty_message'));
    }

    public function stocksEmpty()
    {
        $page_title     = 'Cantidad de existencias bajas';
        $empty_message  = 'Ningún producto disponible aquí';
        $stock_data     = ProductStock::where('quantity', 0)->with('product', function($q){
            return $q->whereHas('categories')->whereHas('brand');
        })->paginate(getPaginate());
        return view('admin.products.items_in_stock', compact('stock_data', 'page_title', 'empty_message'));
    }

}
