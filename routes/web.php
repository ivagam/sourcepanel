<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AiapplicationController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\BrandController;


Route::controller(AuthenticationController::class)->group(function () {
    Route::get('/', 'signin')->name('signin');
    Route::post('/logout', 'logout')->name('logout');

});


// Authentication
Route::prefix('authentication')->group(function () {
    Route::controller(AuthenticationController::class)->group(function () {
        Route::get('/signin', 'signin')->name('signin');
        Route::post('/login', 'login')->name('login');
        Route::get('/login', function () {
            return redirect()->route('signin');
        }); 
         Route::get('/forgotpassword', 'forgotPassword')->name('forgotPassword'); 
        Route::post('/forgotpassword', 'forgotPassword')->name('forgotPassword');  
        Route::match(['get', 'post'], '/forgotPassword', 'forgotPassword')->name('forgotpassword');
        Route::get('/signup', 'signup')->name('signup');        
    });
});

Route::prefix('dashboard')->middleware('auth')->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::get('/sales-data', 'salesData');
        Route::get('/watermark', 'watermark');
    });
});

Route::prefix('users')->middleware('auth')->group(function () {
    Route::controller(UsersController::class)->group(function () {
        Route::get('/addUser', 'addUser')->name('addUser');
        Route::get('/usersList', 'usersList')->name('usersList');
        Route::post('/store', 'store')->name('storeuser');
        Route::get('/editUser/{id}', 'editUser')->name('editUser');
        Route::put('/updateUser/{id}', 'updateUser')->name('updateUser');
        Route::delete('/deleteUser/{id}', 'deleteUser')->name('deleteUser');
        Route::get('/profile/{id}', 'profile')->name('profile');
        Route::post('/changePassword', 'changePassword')->name('changePassword');
    });
});

Route::prefix('category')->middleware('auth')->group(function () {
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/list', 'index')->name('categoryList');
        Route::get('/add', 'create')->name('addcategory');
        Route::post('/store', 'store')->name('storecategory');
        Route::get('/edit/{id}', 'edit')->name('editcategory');
        Route::put('/update/{id}', 'update')->name('updatecategory');
        Route::delete('/delete/{id}', 'destroy')->name('deletecategory');
        Route::get('/get-subcategories/{id}', 'getSubcategories');
        Route::get('/get-watch-subcategories/{id}', 'getWatchSubcategories');
    });
});

Route::prefix('domain')->middleware('auth')->group(function () {
    Route::controller(DomainController::class)->group(function () {
        Route::get('/domain', 'index')->name('domainIndex');
        Route::post('/store', 'store')->name('storeDomain');
        Route::get('/edit/{id}', 'edit')->name('editDomain');
        Route::put('/update/{id}', 'update')->name('updateDomain');
        Route::delete('/delete/{id}', 'destroy')->name('deleteDomain');
    });
});

Route::prefix('customer')->middleware('auth')->group(function () {
    Route::controller(CustomerController::class)->group(function () {
        Route::get('/customer', 'index')->name('customerIndex');      
    });
});

Route::prefix('sales')->middleware('auth')->group(function () {
    Route::controller(SalesController::class)->group(function () {
        Route::get('/sales', 'index')->name('salesIndex');
        Route::get('/salesInvoice/{id}', 'salesInvoice')->name('salesInvoice');      
    });
});

Route::prefix('product')->middleware('auth')->group(function () {
    Route::controller(ProductController::class)->group(function () {
        Route::get('/addProduct', 'addProduct')->name('addProduct');
        Route::get('/productListA', 'productListA')->name('productListA');
        Route::get('/productListB', 'productListB')->name('productListB');
        Route::get('/productListC', 'productListC')->name('productListC');
        Route::post('/store', 'store')->name('storeproduct');
        Route::get('/editProduct/{id}', 'editProduct')->name('editProduct');
        Route::put('/updateProduct/{id}', 'updateProduct')->name('updateProduct');
        Route::delete('/deleteProduct/{id}', 'deleteProduct')->name('deleteProduct');
        Route::get('/by-category/{id}', 'getByCategory')->name('byCategory');
        Route::post('/upload-temp-image', 'uploadTempImage')->name('uploadTempImage');
        Route::post('/delete-image', 'deleteImage')->name('deleteImage');
        Route::post('/product/update-image-order', 'updateImageOrder')->name('updateImageOrder');
        Route::get('/duplicate/{id}', 'duplicateProduct')->name('duplicateProduct');


    });
});


Route::prefix('media')->middleware('auth')->group(function () {
    Route::controller(MediaController::class)->group(function () {
        Route::get('/addMedia', 'addMedia')->name('addMedia');
        Route::get('/mediaList', 'mediaList')->name('mediaList');
        Route::post('/store',  'store')->name('storemedia');
        Route::delete('/destroy/{id}', 'destroy')->name('deletemedia');        
    });
});

Route::prefix('banner')->middleware('auth')->group(function () {
    Route::controller(BannerController::class)->group(function () {
        Route::get('/banner', 'index')->name('bannerIndex');
        Route::post('/store',  'store')->name('storebanner');
        Route::delete('/destroy/{id}', 'destroy')->name('deletebanner');        
    });
});

Route::prefix('brand')->middleware('auth')->group(function () {
    Route::controller(BrandController::class)->group(function () {
        Route::get('/', 'index')->name('brandIndex');
        Route::post('/store', 'store')->name('storebrand');
        Route::delete('/destroy/{id}', 'destroy')->name('deletebrand');
    });
});
