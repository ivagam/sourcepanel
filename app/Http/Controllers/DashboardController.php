<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\StockHelper;
use Carbon\Carbon;
use App\Models\CustomerOrder;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSales = DB::table('customer_orders')->sum('total');        
        $totalCategories = DB::table('category')->count();
        $totalProducts = DB::table('products')->count();
        $totalCustomers = DB::table('customers')->count();
        $totalUsers = DB::table('admins')->count(); 

        $todayOrders = CustomerOrder::with('customer')
            ->whereDate('created_at', Carbon::today())
            ->get();

        return view('dashboard.index2', compact(
            'totalSales',
            'totalCategories', 
            'totalProducts', 
            'totalCustomers', 
            'totalUsers',
            'todayOrders',
        ));
    }

public function salesData(Request $request)
{
    $filter = $request->get('filter', 'today');
    $now = now();

    $categories = [];
    $series = [];

    switch ($filter) {
        case 'today':
            $categories = [$now->format('d M Y')];  // e.g. 18 Jun 2025
            $totalSales = DB::table('customer_orders')
                ->whereDate('created_at', $now)
                ->sum('total');
            $series = [$totalSales];
            break;

        case 'monthly':
            // Show 12 months Jan to Dec of current year with monthly totals
            for ($month = 1; $month <= 12; $month++) {
                $categories[] = \Carbon\Carbon::create($now->year, $month)->format('M');
                $monthTotal = DB::table('customer_orders')
                    ->whereYear('created_at', $now->year)
                    ->whereMonth('created_at', $month)
                    ->sum('total');
                $series[] = $monthTotal;
            }
            $totalSales = array_sum($series);
            break;

        case 'yearly':
            // Optionally show total sales by year for last 5 years or just current year (similar to monthly but by year)
            $startYear = $now->year - 6; // last 5 years
            for ($year = $startYear; $year <= $now->year; $year++) {
                $categories[] = (string)$year;
                $yearTotal = DB::table('customer_orders')
                    ->whereYear('created_at', $year)
                    ->sum('total');
                $series[] = $yearTotal;
            }
            $totalSales = array_sum($series);
            break;

        default:
            $categories = [$now->format('d M Y')];
            $totalSales = DB::table('customer_orders')
                ->whereDate('created_at', $now)
                ->sum('total');
            $series = [$totalSales];
            break;
    }

    return response()->json([
        'totalSales' => round($totalSales, 2),
        'chartData' => [
            'categories' => $categories,
            'series' => $series,
        ]
    ]);
}

    public function index2()
    {
        return view('dashboard/index2');
    }
    
    public function index3()
    {
        return view('dashboard/index3');
    }
    
    public function index4()
    {
        return view('dashboard/index4');
    }
    
    public function index5()
    {
        return view('dashboard/index5');
    }
    
    public function index6()
    {
        return view('dashboard/index6');
    }
    
    public function index7()
    {
        return view('dashboard/index7');
    }
    
    public function index8()
    {
        return view('dashboard/index8');
    }
    
    public function index9()
    {
        return view('dashboard/index9');
    }
    
    public function index10()
    {
        return view('dashboard/index10');
    }

    
}
