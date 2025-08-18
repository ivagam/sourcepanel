<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\StockHelper;
use Carbon\Carbon;
use App\Models\CustomerOrder;
use Intervention\Image\Facades\Image;



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

public function watermark(Request $request)
{
    $uploadPath = public_path('uploads');
     // Full path to the original image
     $filename = '1750748372_ab577fa3cb2059f6a1b0461d66d06f9.jpg';
   $uploadPath = public_path('uploads');
    $imagePath = $uploadPath . '/' . $filename;

    if (!file_exists($imagePath)) {
        return 'Image not found!';
    }

    // Load image
    $info = getimagesize($imagePath);
    $mime = $info['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $img = imagecreatefromjpeg($imagePath);
            break;
        case 'image/png':
            $img = imagecreatefrompng($imagePath);
            break;
        case 'image/gif':
            $img = imagecreatefromgif($imagePath);
            break;
        default:
            return 'Unsupported image type';
    }

    // Text settings
    $text = 'tesrte@gmail.com';
    $fontSize = 40; // in points
    $fontFile = public_path('font/arial.ttf'); // TTF font file
    $white = imagecolorallocatealpha($img, 255, 255, 255, 50); // 50 = semi-transparent

    // Get image width/height
    $imgWidth = imagesx($img);
    $imgHeight = imagesy($img);

    // Calculate text box for centering
    $bbox = imagettfbbox($fontSize, 0, $fontFile, $text);
    $textWidth = $bbox[2] - $bbox[0];
    $textHeight = $bbox[1] - $bbox[7];

    $x = ($imgWidth / 2) - ($textWidth / 2);
    $y = ($imgHeight / 2) + ($textHeight / 2);

    // Add the text
    imagettftext($img, $fontSize, 0, $x, $y, $white, $fontFile, $text);

    // Save image (overwrite original)
    switch ($mime) {
        case 'image/jpeg':
            imagejpeg($img, $imagePath, 90);
            break;
        case 'image/png':
            imagepng($img, $imagePath);
            break;
        case 'image/gif':
            imagegif($img, $imagePath);
            break;
    }

    imagedestroy($img);

    return "Watermark added successfully to $filename";
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
