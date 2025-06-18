<?php

namespace App\Helpers;
use Illuminate\Support\Facades\DB;

class StockHelper
{
    
    public static function getPurchaseUnitPrice($vendor_id, $category_id,$price_unit) {

        $purchased = DB::table('purchase_item')
            ->join('purchase', 'purchase_item.purchase_id', '=', 'purchase.purchase_id')
            ->where('purchase.vendor_id', $vendor_id)
            ->where('category_id', $category_id)
            ->orderBy('purchase_item.purchase_item_id', 'desc') // or any other column to sort by latest
            ->select('purchase_item.*', 'purchase.*') // select fields you need
            ->first();

        $unitPrice = $purchased->amount ?? 0;
        return $unitPrice;

    }

    public static function getPurchasedTotal() {
      
        $totalSales1 = DB::table('sales_item')
            ->select(DB::raw('SUM(weight * purchase_amount) as net_total'))
            ->where('price_unit', 1)
            ->value('net_total');

        $totalSales2 = DB::table('sales_item')
            ->select(DB::raw('SUM(pieces * purchase_amount) as net_total'))
            ->where('price_unit', 2)
            ->value('net_total');
        
        $total = $totalSales1 + $totalSales2;
        return $total;
        
    }

    public static function getPendingStockByVendor($vendor_id, $category_id,$price_unit) {
        // Get total purchased weight
        $pending_stock = 0;
        if($price_unit == 1){
            $purchased = DB::table('purchase_item')
                ->join('purchase', 'purchase_item.purchase_id', '=', 'purchase.purchase_id')
                ->where('purchase.vendor_id', $vendor_id)
                ->where('category_id', $category_id)
                ->sum('weight');
        
            // Get total sold weight
            $sold = DB::table('sales_item')
                ->where('vendor_id', $vendor_id)
                ->where('category_id', $category_id)
                ->sum('weight');
        
            // Calculate pending stock
            $pending_stock = $purchased - $sold;
        
           
        }else{
            $purchased = DB::table('purchase_item')
                ->join('purchase', 'purchase_item.purchase_id', '=', 'purchase.purchase_id')
                ->where('purchase.vendor_id', $vendor_id)
                ->where('category_id', $category_id)
                ->sum('pieces');
        
            // Get total sold weight
            $sold = DB::table('sales_item')
                ->where('vendor_id', $vendor_id)
                ->where('category_id', $category_id)
                ->sum('pieces');
        
            // Calculate pending stock
            $pending_stock = $purchased - $sold;
        
            
        }
        return $pending_stock;
        
    }
    
    public static function getOpeningStock($category_id, $measuring_unit)
    {
        $today = date('Y-m-d');

        if ($measuring_unit == 1) {
            // Sum weight from previous dates
            $purchased = DB::table('purchase_items')
                ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
                ->where('purchase_items.category_id', $category_id)
                ->whereDate('purchases.purchase_date', '<', $today)
                ->sum('purchase_items.weight');

            $sold = DB::table('sales_items')
                ->join('sales', 'sales_items.sales_id', '=', 'sales.id')
                ->where('sales_items.category_id', $category_id)
                ->whereDate('sales.sales_date', '<', $today)
                ->sum('sales_items.weight');

        } elseif ($measuring_unit == 2) {
            // Sum pieces from previous dates
            $purchased = DB::table('purchase_items')
                ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
                ->where('purchase_items.category_id', $category_id)
                ->whereDate('purchases.purchase_date', '<', $today)
                ->sum('purchase_items.pieces');

            $sold = DB::table('sales_items')
                ->join('sales', 'sales_items.sales_id', '=', 'sales.id')
                ->where('sales_items.category_id', $category_id)
                ->whereDate('sales.sales_date', '<', $today)
                ->sum('sales_items.pieces');
        } else {
            return 0;
        }

        return $purchased - $sold;
    }

    function getOpeningStockByCategory($category_id,$unit){
       $purchaseTotal = getPurcahseByCategory($category_id,$unit);
       $salesTotal = getSalesByCategory($category_id,$unit);
       $total = $purchaseTotal - $salesTotal;
       return $total;
    }

    public static function getPurcahseByCategory($category_id,$unit){
        if($unit == 1){
            $row = DB::table('purchase_item')
            ->select(DB::raw('SUM(weight) as total_quantity'))
            ->where('category_id', $category_id)
            ->where('price_unit', $unit)
            ->groupBy('category_id')
            ->first();

            return $row ? $row->total_quantity : 0;
        }else{
            $row = DB::table('purchase_item')
            ->select(DB::raw('SUM(pieces) as total_quantity'))
            ->where('category_id', $category_id)
            ->where('price_unit', $unit)
            ->groupBy('category_id')
            ->first();

            return $row ? $row->total_quantity : 0;
        }
        
    }
    public static function getTodayPurcahseByCategory($category_id,$unit){
        $today = date('Y-m-d'); // define the date first
        $total = 0;
        if($unit == 1){
            $row = DB::table('purchase_item')
            ->select(
                'category_id',
                DB::raw('SUM(weight) as total_quantity')
            )
            ->where('category_id', $category_id)
            ->where('price_unit', $unit)
            ->whereDate('created_at', $today)
            ->groupBy('category_id')
            ->first();
            if($row){
                $total = $row->total_quantity;
            }
        }else{
            $row = DB::table('purchase_item')
            ->select(
                'category_id',
                DB::raw('SUM(pieces) as total_quantity')
            )
            ->where('category_id', $category_id)
            ->where('price_unit', $unit)
            ->whereDate('created_at', $today)
            ->groupBy('category_id')
            ->first();
            if($row){
                $total = $row->total_quantity;
            }
        }
       
        return $total;
    }
    public static function getSalesByCategory($category_id,$unit){
        $total = 0;
        if($unit == 1){
            $row = DB::table('sales_item')
            ->select('category_id', DB::raw('SUM(weight) as total_quantity'))
            ->where('category_id', $category_id)
            ->where('price_unit', $unit)
            ->groupBy('category_id')
            ->first();
            if($row){
                $total = $row->total_quantity;
            }
        }else{
            $row = DB::table('sales_item')
            ->select('category_id', DB::raw('SUM(pieces) as total_quantity'))
            ->where('category_id', $category_id)
            ->where('price_unit', $unit)
            ->groupBy('category_id')
            ->first();
            if($row){
                $total = $row->total_quantity;
            }
        }
       
        
        return $total;        
    }
    

    public static function getIndentItemList($unit)
    {
        $arrObject = array();
        $today = date('Y-m-d'); // define the date first
        $data = "";
        if($unit == 1){
            $data = DB::table('indent_items')
            ->leftJoin('category', 'indent_items.category_id', '=', 'category.category_id')
            ->select(
                'indent_items.category_id',
                'category.category_name',
                DB::raw('SUM(indent_items.weight) as total_weight'),
            )
            ->where('indent_items.quantity_unit', $unit)
            ->whereDate('indent_items.created_at', $today)
            ->groupBy('indent_items.category_id', 'category.category_name')
            ->get();
        }else{
            $data = DB::table('indent_items')
            ->leftJoin('category', 'indent_items.category_id', '=', 'category.category_id')
            ->select(
                'indent_items.category_id',
                'category.category_name',
                DB::raw('SUM(indent_items.pieces) as total_weight'),
            )
            ->where('indent_items.quantity_unit', $unit)
            ->whereDate('indent_items.created_at', $today)
            ->groupBy('indent_items.category_id', 'category.category_name')
            ->get();
        }
        if($data){
            foreach($data as $row){
                $todayPurchase = StockHelper::getTodayPurcahseByCategory($row->category_id,$unit);
                $totalPurchase = StockHelper::getPurcahseByCategory($row->category_id,$unit);
               
                $totalSales =  StockHelper::getSalesByCategory($row->category_id,$unit);
                
                $opening_stock = $totalPurchase - ($todayPurchase + $totalSales);

                $require = $row->total_weight - ($opening_stock + $todayPurchase);
                $minData = array('category_id'=>$row->category_id,'category_name'=>$row->category_name,'indent_quantity'=>$row->total_weight,'today_purchase'=>$todayPurchase,'opening_stack'=>$opening_stock,'require_item'=>$require);
                $arrObject[] = $minData;
    
            }
        }
      
        return $arrObject;
    }

public static function getPendingStockWeight()
{
    $categories = DB::table('category')->get();

    $report = [];
    
    foreach ($categories as $cat) {
        $purchaseItems = DB::table('purchase_item')
            ->where('category_id', $cat->category_id)
            ->select(DB::raw('SUM(weight) as total_weight'), DB::raw('SUM(weight * amount) as total_cost'))
            ->where('price_unit',1)
            ->first();
    
        $salesItems = DB::table('sales_item')
            ->where('category_id', $cat->category_id)
            ->select(DB::raw('SUM(weight) as total_weight'), DB::raw('SUM(weight * amount) as total_sale'))
            ->where('price_unit',1)
            ->first();
    
        $purchase_weight = $purchaseItems->total_weight ?? 0;
        $purchase_cost = $purchaseItems->total_cost ?? 0;
    
        $sale_weight = $salesItems->total_weight ?? 0;
        $sale_amount = $salesItems->total_sale ?? 0;
    
        $weighted_avg_purchase_price = $purchase_weight > 0 ? $purchase_cost / $purchase_weight : 0;
    
        $sold_stock_cost = $sale_weight * $weighted_avg_purchase_price;
        $pending_stock = $purchase_weight - $sale_weight;
        $pending_stock_cost = $pending_stock * $weighted_avg_purchase_price;
    
        $profit = $sale_amount - $sold_stock_cost;
        if($purchase_weight > 0){
            $report[] = [
                'category_name' => $cat->category_name,
                'purchased_weight' => $purchase_weight,
                'total_purchase_cost' => $purchase_cost,
                'sold_weight' => $sale_weight,
                'total_sale_amount' => $sale_amount,
                'pending_stock' => $pending_stock,
                'pending_stock_cost' => $pending_stock_cost,
                'sold_stock_cost' => $sold_stock_cost,
                'profit' => $profit
            ];
        }
        
    }

    return  $report;
}


public static function getPendingStockPieces()
{
    $categories = DB::table('category')->get();

    $report = [];
    
    foreach ($categories as $cat) {
        $purchaseItems = DB::table('purchase_item')
            ->where('category_id', $cat->category_id)
            ->where('price_unit',2)
            ->select(DB::raw('SUM(pieces) as total_weight'), DB::raw('SUM(pieces * amount) as total_cost'))
            ->first();
    
        $salesItems = DB::table('sales_item')
            ->where('category_id', $cat->category_id)
            ->where('price_unit',2)
            ->select(DB::raw('SUM(pieces) as total_weight'), DB::raw('SUM(pieces * amount) as total_sale'))
            ->first();
    
        $purchase_weight = $purchaseItems->total_weight ?? 0;
        $purchase_cost = $purchaseItems->total_cost ?? 0;
    
        $sale_weight = $salesItems->total_weight ?? 0;
        $sale_amount = $salesItems->total_sale ?? 0;
    
        $weighted_avg_purchase_price = $purchase_weight > 0 ? $purchase_cost / $purchase_weight : 0;
    
        $sold_stock_cost = $sale_weight * $weighted_avg_purchase_price;
        $pending_stock = $purchase_weight - $sale_weight;
        $pending_stock_cost = $pending_stock * $weighted_avg_purchase_price;
    
        $profit = $sale_amount - $sold_stock_cost;
    
        if($purchase_weight > 0){
            $report[] = [
                'category_name' => $cat->category_name,
                'purchased_weight' => $purchase_weight,
                'total_purchase_cost' => $purchase_cost,
                'sold_weight' => $sale_weight,
                'total_sale_amount' => $sale_amount,
                'pending_stock' => $pending_stock,
                'pending_stock_cost' => $pending_stock_cost,
                'sold_stock_cost' => $sold_stock_cost,
                'profit' => $profit
            ];
        }
    }

    return  $report;

}

public static function getReturnQuantity($categoryId, $priceUnit, $salesId)
    {
        // Example logic — update as needed
        $query = ReturnItem::where('sales_id', $salesId)
            ->where('category_id', $categoryId);

        return $priceUnit == 1
            ? $query->sum('weight')
            : $query->sum('pieces');
    }

}
