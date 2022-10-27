<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProfitController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\TransactionController;
use App\Models\Employee;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/',[AuthController::class,'index']);

Route::get('/tes', function () {
    return view('welxxxcome');
});
Route::get("/",[AuthController::class,"index"])->name('login');
Route::post('/login',[AuthController::class,'login'])->name('loginProcess');
Route::get("/logout",[AuthController::class,"logout"])->name('logout');

Route::group(['prefix'=>'employee','as'=>'employee.','middleware'=>['LoginMiddleware']],function(){
    Route::get('/',[EmployeeController::class,'getDashboard'])->name('getDashboard');
});

Route::group(['prefix'=>'admin','as'=>'admin.','middleware'=>['LoginMiddleware']],function(){
    Route::get('dashboard',[AdminController::class,'index'])->name('dashboard');
    Route::get('akun/pengaturan/',[AuthController::class,'accountSetting'])->name('accountSetting');
    Route::post('akun/update/',[AuthController::class,'updateSetting'])->name('updateSetting');
    Route::group(['prefix'=>'transaksi',],function(){
        Route::get('/',[TransactionController::class,'index'])->name('transaction');
        Route::get('detail/pencucian/{type}',[TransactionController::class,'detailPencucian'])->name('detail.pencucian');
        Route::get('detail/harga/{id}',[TransactionController::class,'priceDetail'])->name('price.detail');
        Route::post('/tambah',[TransactionController::class,'insertTransaction'])->name('transaction.insert');
    });
    Route::group(['prefix'=>'tarif','as'=>'rate.','middleware'=>['AdminMiddleware']],function(){
        Route::get('/',[RateController::class,'index'])->name('index');
        Route::get('/edit/{id}',[RateController::class,'edit'])->name('edit');
        Route::post('/update/{id}',[RateController::class,'update'])->name('update');
    });
    Route::group(['prefix'=>'profit','as'=>'profit.','middleware'=>['AdminMiddleware']],function(){
        Route::get('/',[ProfitController::class,'index'])->name('index');
        Route::get('/date/{date}',[ProfitController::class,'selectDate'])->name('selectDate');
        Route::get('/table/{date}',[ProfitController::class,'showTable'])->name('showTable');
        Route::post('insert/{date}',[ProfitController::class,'insertProfit'])->name('insertProfit');
        Route::get('karyawan/fee/{date}',[ProfitController::class,'fee'])->name('fee');
    });
    Route::group(['prefix'=>'karyawan','as'=>'employee.','middleware'=>['AdminMiddleware']],function(){
        Route::get('/',[EmployeeController::class,'index'])->name('index');
        Route::get('/get/all',[EmployeeController::class,'getAll'])->name('getAll');
        Route::post('/insert',[EmployeeController::class,'insert'])->name('insert');
        Route::post('/set/fee',[EmployeeController::class,'setTotalFee'])->name('setTotalFee');
        Route::post('insert/employee/',[EmployeeController::class,'insertEmployee'])->name('insertEmployee');
    });

    Route::group(['prefix'=>'rekap','as'=>'rekap.','middleware'=>['AdminMiddleware']],function(){
       Route::get('/',[RekapController::class,'index'])->name('index');
       Route::post('/sort/date',[RekapController::class,'getBetweenDate'])->name('getBetweenDate');
       Route::get('rekapan/',[RekapController::class,'export'])->name('export');
    });

    Route::group(['prefix'=>'booking','as'=>'booking.','middleware'=>['AdminMiddleware']],function(){
        Route::get('/',[BookingController::class,'index'])->name('index');
        Route::get('done/{id}',[BookingController::class,'done'])->name('done');
    });

});

Route::group(['prefix'=>'booking','as'=>'booking.'],function(){
    Route::get('/',[BookingController::class,'costumerBook'])->name('index');
    Route::post('insert',[BookingController::class,'insert'])->name('insert');
    Route::get('success',[BookingController::class,'success'])->name('success');
    Route::get('verification',[BookingController::class,'verification'])->name('verification');
    Route::post('verification/check',[BookingController::class,'verification_check'])->name('verification_check');
});
Route::get('detail/pencucian/{type}',[TransactionController::class,'detailPencucian'])->name('detail.pencucian');
Route::get('detail/harga/{id}',[TransactionController::class,'priceDetail'])->name('price.detail');

