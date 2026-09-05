<?php

use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\BuildingController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\PurposeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

// Login
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SMSController;
use App\Http\Controllers\Admin\Telecaller;
use App\Http\Controllers\CachesController;
use App\Http\Controllers\TestingController;
use App\Http\Controllers\BackendIndexController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Telecaller\TelecallerController;

Route::get('/paginate', function () {
    return view('vendor.pagination.default');
});

Route::get('/notify', [TestingController::class, 'notify']);

// Clear Cache
Route::get('/clearcache', [CachesController::class, 'caches']);
Route::get('/chart', [TestingController::class, 'chart']);

// Route::get('/mail', function () {
//     Mail::to('helptogethermedia@gmail.com')
//         ->send(new SendMail());
//     // return view('welcome');
// });

//LoginPage===========--------------===========>>>>
Route::get('/', [LoginController::class, 'login'])->name('login');
Route::post('/admin', [LoginController::class, 'loginPost'])->name('login.post');
Route::get('admin/logout', [LoginController::class, 'logout'])->name('adminLogout');

Route::get('/dashboard', [BackendIndexController::class, 'index'])->name('dashboard')->middleware('auth');

//Authentication
Route::group(['middleware' => ['auth', 'role:1']], function () {

    Route::get('/admin/dashboard', [BackendIndexController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/dashboard/filter', [BackendIndexController::class, 'paymentFilter'])->name('dashboard.payment.filter');
    Route::get('/admin/dashboard/export-excel', [BackendIndexController::class, 'exportExcel'])->name('dashboard.export.excel');
    Route::get('/admin/dashboard/export-pdf', [BackendIndexController::class, 'exportPdf'])->name('dashboard.export.pdf');

    // Route::get('/dashboard', [DashboardController::class, 'dashboard']);
    Route::get('/index', [DashboardController::class, 'index'])->name('index');

    Route::get('/admin/user/register', [LoginController::class, 'register'])->name('admin.register');
    Route::post('/admin/user/register', [LoginController::class, 'store'])->name('admin.register.store');
    Route::get('/admin/users', [AdminController::class, 'listUsers'])->name('admin.users');
    // Route::get('/search-filter', [AdminController::class, 'filter'])->name('filter');
    Route::get('/admin/user/delete/{id}', [AdminController::class, 'delete']);

    Route::get('/addnew', [DashboardController::class, 'addNew'])->name('addnew');
    Route::get('/renew', [DashboardController::class, 'renew'])->name('renew');
    Route::post('/addnew', [DashboardController::class, 'store'])->name('addnew.store');
    Route::get('/entry/{id}/edit', [DashboardController::class, 'edit'])->name('entry.edit');
    Route::patch('/entry/{id}', [DashboardController::class, 'update'])->name('entry.update');
    Route::get('/entry/delete/{id}', [DashboardController::class, 'delete'])->name('entry.delete');
    Route::get('/sendMail', [DashboardController::class, 'sendMail']);
    Route::get('/search-filter', [DashboardController::class, 'filter'])->name('search.filter');
    Route::get('/get-company-suggestions', [DashboardController::class, 'getCompanySuggestions']);
    Route::get('/get-company-data/{name}', [DashboardController::class, 'getCompanyData'])->name('company.data');

    Route::get('/historyPage', [DashboardController::class, 'index'])->name('historyPage');

    // SMS
    Route::post('/send-sms', [SMSController::class, 'sendSms']);
    Route::get('/download-multiple/{id}', [DashboardController::class, 'downloadImages'])->name('download');
    Route::get('/export-contracts', [DashboardController::class, 'export'])->name('export.contracts');

    // Telecaller
    Route::get('/admin/telecaller/addnew', [Telecaller::class, 'create'])->name('admin.telecaller');
    Route::post('/admin/telecaller/addnew', [Telecaller::class, 'store'])->name('admin.telecaller.store');
    Route::get('/admin/telecallers', [Telecaller::class, 'index'])->name('admin.telecallers');
    Route::get('/admin/telecaller/delete/{id}', [Telecaller::class, 'delete']);

    // Bank
    Route::get('/admin/bank', [BankController::class, 'index'])->name('bank.index');
    Route::get('/admin/bank/create', [BankController::class, 'create'])->name('bank.create');
    Route::post('/admin/bank/store', [BankController::class, 'store'])->name('bank.store');
    Route::get('/admin/bank/edit/{bank}', [BankController::class, 'edit'])->name('bank.edit');
    Route::post('/admin/bank/update/{bank}', [BankController::class, 'update'])->name('bank.update');
    Route::delete('/bank/delete/{bank}', [BankController::class, 'destroy'])->name('bank.destroy');

    // Purpose
    Route::get('/admin/purpose', [PurposeController::class, 'index'])->name('purpose.index');
    Route::get('/admin/purpose/create', [PurposeController::class, 'create'])->name('purpose.create');
    Route::post('/admin/purpose/store', [PurposeController::class, 'store'])->name('purpose.store');
    Route::get('/admin/purpose/edit/{purpose}', [PurposeController::class, 'edit'])->name('purpose.edit');
    Route::post('/admin/purpose/update/{purpose}', [PurposeController::class, 'update'])->name('purpose.update');
    Route::delete('/purpose/delete/{purpose}', [PurposeController::class, 'destroy'])->name('purpose.destroy');

    // Expense
    Route::get('/admin/expense', [ExpenseController::class, 'index'])->name('expense.index');
    Route::get('/admin/expense/create', [ExpenseController::class, 'create'])->name('expense.create');
    Route::post('/admin/expense/store', [ExpenseController::class, 'store'])->name('expense.store');
    Route::get('/admin/expense/edit/{expense}', [ExpenseController::class, 'edit'])->name('expense.edit');
    Route::post('/admin/expense/update/{expense}', [ExpenseController::class, 'update'])->name('expense.update');
    Route::delete('/expense/delete/{expense}', [ExpenseController::class, 'destroy'])->name('expense.destroy');

    Route::get('/admin/leads', [AdminController::class, 'assignedLeads'])->name('leads');
    Route::get('/admin/entry/from-lead/{id}', [AdminController::class, 'createFromLead'])->name('entry.fromLead');
    Route::put('/admin/leads/{lead}/status', [AdminController::class, 'updateStatus'])->name('leads.updateStatus');

    // for leads CURD
    Route::get('/admin/lead/index', [AdminController::class, 'index'])->name('admin.lead.index');
    Route::get('/admin/lead/create', [AdminController::class, 'create'])->name('admin.lead.create');
    Route::post('/admin/lead/create', [AdminController::class, 'store'])->name('admin.lead.store');
    Route::get('/admin/lead/{id}/edit', [AdminController::class, 'edit'])->name('admin.lead.edit');
    Route::patch('/admin/lead/{id}', [AdminController::class, 'update'])->name('admin.lead.update');
    Route::get('/admin/lead/delete/{id}', [AdminController::class, 'delete1'])->name('admin.lead.delete');

    // for room rent
    Route::get('/admin/rent/index', [BuildingController::class, 'index'])->name('admin.rent.index');
    Route::get('/admin/rent/create', [BuildingController::class, 'create'])->name('admin.rent.create');
    Route::post('/admin/rent/create', [BuildingController::class, 'store'])->name('admin.rent.store');
    Route::get('/admin/rent/{id}/edit', [BuildingController::class, 'edit'])->name('admin.rent.edit');
    Route::patch('/admin/rent/{id}', [BuildingController::class, 'update'])->name('admin.rent.update');
    Route::get('/admin/rent/delete/{id}', [BuildingController::class, 'delete1'])->name('admin.rent.delete');
});

// User Routes
Route::group(['middleware' => ['auth', 'role:2'], 'as' => 'user.'], function () {

    Route::get('/user/dashboard', [BackendIndexController::class, 'index'])->name('dashboard');
    Route::get('/user/index', [UserController::class, 'index'])->name('index');

    Route::get('/user/leads', [UserController::class, 'assignedLeads'])->name('leads');
    Route::get('/user/entry/from-lead/{id}', [UserController::class, 'createFromLead'])->name('entry.fromLead');
    Route::put('/user/leads/{lead}/status', [UserController::class, 'updateStatus'])->name('leads.updateStatus');

    Route::get('/user/addnew', [UserController::class, 'addNew'])->name('addnew');
    Route::get('/user/renew', [UserController::class, 'renew'])->name('renew');
    Route::post('/user/addnew', [UserController::class, 'store'])->name('addnew.store');
    Route::get('/user/entry/{id}/edit', [UserController::class, 'edit'])->name('entry.edit');
    Route::patch('/user/entry/{id}', [UserController::class, 'update'])->name('entry.update');
    Route::get('/user/entry/delete/{id}', [UserController::class, 'delete'])->name('entry.delete');
    Route::get('/user/sendMail', [UserController::class, 'sendMail']);
    Route::get('/user/search-filter', [UserController::class, 'filter'])->name('search.filter');
    Route::get('/user/get-company-suggestions', [UserController::class, 'getCompanySuggestions']);
    Route::get('/user/get-company-data/{name}', [UserController::class, 'getCompanyData'])->name('company.data');
    Route::get('/user/download-multiple/{id}', [UserController::class, 'downloadImages'])->name('download');
    Route::get('/user/export-contracts', [UserController::class, 'export'])->name('export.contracts');

    // for leads CURD
    Route::get('/user/lead/index', [UserController::class, 'index1'])->name('lead.index');
    Route::get('/user/lead/create', [UserController::class, 'create1'])->name('lead.create');
    Route::post('/user/lead/create', [UserController::class, 'store1'])->name('lead.store');
    Route::get('/user/lead/{id}/edit', [UserController::class, 'edit1'])->name('lead.edit');
    Route::patch('/user/lead/{id}', [UserController::class, 'update1'])->name('lead.update');
    Route::get('/user/lead/delete/{id}', [UserController::class, 'delete1'])->name('lead.delete');

});

// Telecaller Routes
Route::group(['middleware' => ['auth', 'role:3'], 'as' => 'telecaller.'], function () {

    Route::get('/telecaller/dashboard', [BackendIndexController::class, 'index'])->name('dashboard');
    Route::get('/telecaller/index', [TelecallerController::class, 'index'])->name('index');

    Route::get('/telecaller/create', [TelecallerController::class, 'create'])->name('create');
    Route::post('/telecaller/create', [TelecallerController::class, 'store'])->name('store');
    Route::get('/telecaller/{id}/edit', [TelecallerController::class, 'edit'])->name('edit');
    Route::patch('/telecaller/{id}', [TelecallerController::class, 'update'])->name('update');
    Route::get('/telecaller/delete/{id}', [TelecallerController::class, 'delete'])->name('delete');

});
