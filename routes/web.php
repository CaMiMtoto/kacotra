<?php

use App\Http\Controllers\BankController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DamageController;
use App\Http\Controllers\DefaultController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\MethodController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('/customers', CustomerController::class);
    Route::resource('/suppliers', SupplierController::class);
    Route::resource('/categories', CategoryController::class);
    Route::resource('/banks', BankController::class);
    Route::resource('/methods', MethodController::class);
    Route::resource('/departments', DepartmentController::class);
    Route::resource('/units', UnitController::class);

    // Route Products
    Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');
    Route::get('/products/import', [ProductController::class, 'import'])->name('products.import');
    Route::post('/products/import', [ProductController::class, 'handleImport'])->name('products.handleImport');
    Route::resource('/products', ProductController::class);
    Route::get('/products/delete/{product_id}', [ProductController::class, 'deleteProduct'])->name('product.deleteProduct');
    Route::get('/products/restore/{product_id}', [ProductController::class, 'restoreProduct'])->name('product.restoreProduct');

    // Route Issues
    Route::get('/issues/export', [IssueController::class, 'export'])->name('issues.export');
    Route::get('/issues/import', [IssueController::class, 'import'])->name('issues.import');
    Route::post('/issues/import', [IssueController::class, 'handleImport'])->name('issues.handleImport');
    Route::resource('/issues', IssueController::class);

    // Route Deposits
    Route::get('/deposits/export', [DepositController::class, 'export'])->name('deposits.export');
    Route::get('/deposits/import', [DepositController::class, 'import'])->name('deposits.import');
    Route::post('/deposits/import', [DepositController::class, 'handleImport'])->name('deposits.handleImport');
    Route::resource('/deposits', DepositController::class);

    // Route Cashes
    Route::get('/cashes/export', [CashController::class, 'export'])->name('cashes.export');
    Route::get('/cashes/import', [CashController::class, 'import'])->name('cashes.import');
    Route::post('/cashes/import', [CashController::class, 'handleImport'])->name('cashes.handleImport');
    Route::resource('/cashes', CashController::class);

    // Route Stocks
    Route::get('/stocks/filter', [StockController::class, 'filter'])->name('stocks.filter');
    Route::get('/stocks/export', [StockController::class, 'export'])->name('stocks.export');
    Route::get('/stocks/import', [StockController::class, 'import'])->name('stocks.import');
    Route::post('/stocks/import', [StockController::class, 'handleImport'])->name('stocks.handleImport');
    Route::resource('/stocks', StockController::class);
    Route::get('/stocks/delete/{stock_id}', [StockController::class, 'deleteStock'])->name('stocks.deleteStock');

    // Route Journals
    Route::get('/journals/filter', [JournalController::class, 'filter'])->name('journals.filter');
    Route::get('/journals/export', [JournalController::class, 'export'])->name('journals.export');
    Route::get('/journals/import', [JournalController::class, 'import'])->name('journals.import');
    Route::post('/journals/import', [JournalController::class, 'handleImport'])->name('journals.handleImport');
    Route::resource('/journals', JournalController::class);
    Route::get('/journals/delete/{journal_id}', [JournalController::class, 'deleteJournal'])->name('journal.deleteJournal');

    // Route POS
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/cart/add', [PosController::class, 'addCartItem'])->name('pos.addCartItem');
    Route::post('/pos/cart/update/{rowId}', [PosController::class, 'updateCartItem'])->name('pos.updateCartItem');
    Route::delete('/pos/cart/delete/{rowId}', [PosController::class, 'deleteCartItem'])->name('pos.deleteCartItem');
    Route::post('/pos/invoice', [PosController::class, 'createInvoice'])->name('pos.createInvoice');

    Route::post('/pos', [OrderController::class, 'createOrder'])->name('pos.createOrder');

    // Route Orders
    Route::get('/orders', [OrderController::class, 'allOrders'])->name('orders.allOrders');
    Route::get('/orders/filter', [OrderController::class, 'filter'])->name('orders.filter');
    Route::get('/orders/week', [OrderController::class, 'currentWeekOrders'])->name('orders.currentWeekOrders');
    Route::get('/orders/month', [OrderController::class, 'currentMonthOrders'])->name('orders.currentMonthOrders');
    Route::get('/orders/year', [OrderController::class, 'currentYearOrders'])->name('orders.currentYearOrders');
    Route::get('/orders/create', [OrderController::class, 'createOrder'])->name('orders.createOrder');
    Route::get('/orders/pending', [OrderController::class, 'pendingOrders'])->name('order.pendingOrders');
    Route::get('/orders/pending/{order_id}', [OrderController::class, 'orderDetails'])->name('order.orderPendingDetails');
    Route::get('/orders/complete', [OrderController::class, 'completeOrders'])->name('order.completeOrders');
    Route::get('/orders/complete/{order_id}', [OrderController::class, 'orderDetails'])->name('order.orderCompleteDetails');
    Route::get('/orders/details/{order_id}', [OrderController::class, 'orderReportDetails'])->name('order.orderReportDetails');
    Route::get('/orders/details/{order_id}/download', [OrderController::class, 'downloadInvoice'])->name('order.downloadInvoice');
    Route::get('/orders/due', [OrderController::class, 'dueOrders'])->name('order.dueOrders');
    Route::get('/orders/due/pay/{order_id}', [OrderController::class, 'dueOrderDetails'])->name('order.dueOrderDetails');
    /* Route::put('/orders/due/pay/update', [OrderController::class, 'updateDueOrder'])->name('order.updateDueOrder'); */
    Route::put('/orders/due/pay/update', [OrderController::class, 'updateOrderDue'])->name('order.updateOrderDue');
    Route::put('/orders/due/pay/pay-due', [OrderController::class, 'payDueOrder'])->name('order.payDueOrder');
    Route::get('/orders/refund', [OrderController::class, 'refundOrders'])->name('order.refundOrders');
    Route::get('/orders/refund/pay/{order_id}', [OrderController::class, 'refundOrderDetails'])->name('order.refundOrderDetails');
    Route::put('/orders/refund/pay/update', [OrderController::class, 'updateRefundOrder'])->name('order.updateRefundOrder');
    Route::put('/orders/update', [OrderController::class, 'updateOrder'])->name('order.updateOrder');
    Route::get('/orders/delete/{order_id}', [OrderController::class, 'deleteOrder'])->name('order.deleteOrder');
    Route::get('/orders/restore/{order_id}', [OrderController::class, 'restoreOrder'])->name('order.restoreOrder');

    Route::get('/orders/report', [OrderController::class, 'dailyOrderReport'])->name('orders.dailyOrderReport');
    Route::get('/orders/report/product', [OrderController::class, 'dailyProductOrderReport'])->name('orders.dailyProductOrderReport');
    Route::get('/orders/report/cash', [OrderController::class, 'dailyCashReport'])->name('orders.dailyCashReport');
    Route::get('/orders/report/cash/filter', [OrderController::class, 'filterCashReport'])->name('orders.filterCashReport');
    Route::get('/orders/report/cash/current-week', [OrderController::class, 'currentWeekCashReport'])->name('orders.currentWeekCashReport');
    Route::get('/orders/report/cash/current-month', [OrderController::class, 'currentMonthCashReport'])->name('orders.currentMonthCashReport');
    Route::get('/orders/report/cash/current-year', [OrderController::class, 'currentYearCashReport'])->name('orders.currentYearCashReport');
    Route::get('/orders/report/export', [OrderController::class, 'getOrderReport'])->name('orders.getOrderReport');
    Route::post('/orders/report/export', [OrderController::class, 'exportOrderReport'])->name('orders.exportOrderReport');

    // Default Controller
    Route::get('/get-all-product', [DefaultController::class, 'GetProducts'])->name('get-all-product');
    Route::get('/get-all-issue', [DefaultController::class, 'GetIssues'])->name('get-all-issue');
    Route::get('/get-all-deposit', [DefaultController::class, 'GetDeposits'])->name('get-all-deposit');

    // Route Purchases
    Route::get('/purchases', [PurchaseController::class, 'allPurchases'])->name('purchases.allPurchases');
    Route::get('/purchases/pending', [PurchaseController::class, 'pendingPurchases'])->name('purchases.pendingPurchases');
    Route::get('/purchases/approved', [PurchaseController::class, 'approvedPurchases'])->name('purchases.approvedPurchases');
    Route::get('/purchases/create', [PurchaseController::class, 'createPurchase'])->name('purchases.createPurchase');
    Route::post('/purchases', [PurchaseController::class, 'storePurchase'])->name('purchases.storePurchase');
    Route::put('/purchases/update', [PurchaseController::class, 'updatePurchase'])->name('purchases.updatePurchase');
    Route::get('/purchases/details/{purchase_id}', [PurchaseController::class, 'purchaseDetails'])->name('purchases.purchaseDetails');
    Route::get('/purchases/delete/{purchase_id}', [PurchaseController::class, 'deletePurchase'])->name('purchases.deletePurchase');

    Route::get('/purchases/report', [PurchaseController::class, 'dailyPurchaseReport'])->name('purchases.dailyPurchaseReport');
    Route::get('/purchases/report/export', [PurchaseController::class, 'getPurchaseReport'])->name('purchases.getPurchaseReport');
    Route::post('/purchases/report/export', [PurchaseController::class, 'exportPurchaseReport'])->name('purchases.exportPurchaseReport');

    // Route Expenses
    Route::get('/expenses', [ExpenseController::class, 'allExpenses'])->name('expenses.allExpenses');
    Route::get('/expenses/pending', [ExpenseController::class, 'pendingExpenses'])->name('expenses.pendingExpenses');
    Route::get('/expenses/approved', [ExpenseController::class, 'approvedExpenses'])->name('expenses.approvedExpenses');
    Route::get('/expenses/create', [ExpenseController::class, 'createExpense'])->name('expenses.createExpense');
    Route::post('/expenses', [ExpenseController::class, 'storeExpense'])->name('expenses.storeExpense');
    Route::put('/expenses/update', [ExpenseController::class, 'updateExpense'])->name('expenses.updateExpense');
    Route::get('/expenses/details/{expense_id}', [ExpenseController::class, 'expenseDetails'])->name('expenses.expenseDetails');
    Route::get('/expenses/delete/{expense_id}', [ExpenseController::class, 'deleteExpense'])->name('expenses.deleteExpense');

    Route::get('/expenses/report', [ExpenseController::class, 'dailyExpenseReport'])->name('expenses.dailyExpenseReport');
    Route::get('/expenses/report/export', [ExpenseController::class, 'getExpenseReport'])->name('expenses.getExpenseReport');
    Route::post('/expenses/report/export', [ExpenseController::class, 'exportExpenseReport'])->name('expenses.exportExpenseReport');

    // Route Damages
    Route::get('/damages', [DamageController::class, 'allDamages'])->name('damages.allDamages');
    Route::get('/damages/approved', [DamageController::class, 'approvedDamages'])->name('damages.approvedDamages');
    Route::get('/damages/create', [DamageController::class, 'createDamage'])->name('damages.createDamage');
    Route::post('/damages', [DamageController::class, 'storeDamage'])->name('damages.storeDamage');
    Route::put('/damages/update', [DamageController::class, 'updateDamage'])->name('damages.updateDamage');
    Route::get('/damages/details/{damage_id}', [DamageController::class,
    'damageDetails'])->name('damages.damageDetails');
    Route::delete('/damages/delete/{damage_id}', [DamageController::class,
    'deleteDamage'])->name('damages.deleteDamage');

    Route::get('/damages/report', [DamageController::class, 'dailyDamageReport'])->name('damages.dailyDamageReport');
    Route::get('/damages/report/export', [DamageController::class, 'getDamageReport'])->name('damages.getDamageReport');
    Route::post('/damages/report/export', [DamageController::class,
    'exportDamageReport'])->name('damages.exportDamageReport');


    // User Management
    Route::resource('/users', UserController::class)->except(['show']);
    Route::put('/user/change-password/{username}', [UserController::class, 'updatePassword'])->name('users.updatePassword');
});

require __DIR__.'/auth.php';
