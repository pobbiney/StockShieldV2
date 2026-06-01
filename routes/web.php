<?php


use App\Http\Controllers\Asset\AssetController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication\AuthenticationController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\UserManagement\UserManagementController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Stock\StockController;
 

/*  Frontend */
Route::get('/',[AuthenticationController::class,'frontend'])->name('login');
Route::get('register',[AuthenticationController::class,'Register'])->name('register');
Route::post('authentication-account-process',[AuthenticationController::class,'RegisterAccount'])->name('authentication-account-process');
Route::get('confirmation/{id}',[AuthenticationController::class,'getConfirmationPage'])->name('confirmation');
Route::post('frontend-login',[AuthenticationController::class,'loginAccount'])->name('frontend-login');
Route::get('applicant-dashboard',[AuthenticationController::class,'getApplicantDashboard'])->name('applicant-dashboard');
Route::get('/logout', [AuthenticationController::class, 'logout'])->name('logout');
Route::get('/applicant-dashboard', function () {
    return view('applicant-dashboard');
})->middleware('auth')->name('applicant-dashboard');

 

Route::get('forgot-password',[AuthenticationController::class,'getForgetPassword'])->name('forgot-password');
Route::post('forgot-password-process',[AuthenticationController::class,'forgotPass'])->name('forgot-password-process');
Route::get('verify-otp-page/{id}',[AuthenticationController::class,'getOtp'])->name('verify-otp-page');
Route::post('update-user-photo-process',[AuthenticationController::class,'updatePhoto'])->name('update-user-photo-process');
Route::post('update-user-password-process',[AuthenticationController::class,'updatePassword'])->name('update-user-password-process');
Route::post('reset-otp-process/{id}',[AuthenticationController::class,'resetOtp'])->name('reset-otp-process');

/** End of Frontend */

/* Backend*/
Route::get('/',[AuthenticationController::class,'getAdminLoginPage'])->name('admin-login');
Route::get('dashboard',[DashboardController::class,'index'])->name('dashboard');
Route::get('theme-settings',[DashboardController::class,'themeSettings'])->name('theme-settings');
Route::get('user-profile',[AuthenticationController::class,'getUserProfile'])->name('user-profile');
/* Authentication */ 

Route::post('authentication-process',[AuthenticationController::class,'authenticationProcess'])->name('authentication-process');
Route::post('logout-authentication-process',[DashboardController::class,'logoutAuthenticationProcess'])->name('logout-authentication-process');

/** Staff Manager */
Route::get('create-staff',[StaffController::class,'addStaffView'])->name('create-staff');
Route::post('add-staff-process',[StaffController::class,'addStaff'])->name('add-staff-process');
Route::get('list-staff',[StaffController::class,'getStaffListView'])->name('list-staff');
/** End of Staff Manager */


/* User Management */ 

Route::get('user-management-add-category',[UserManagementController::class,'userCategoryView'])->name('user-management-add-category');
Route::post('user-management-add-category-process',[UserManagementController::class,'userCategoryProcess'])->name('user-management-add-category-process');
Route::get('user-management-add-category-edit/{id}',[UserManagementController::class,'editUserCategoryView'])->name('user-management-add-category-edit');
Route::post('user-management-add-category-edit-process/{id}',[UserManagementController::class,'editUserCategoryProcess'])->name('user-management-add-category-edit-process');

Route::get('user-management-privilege',[UserManagementController::class,'assignUserPrivilegesView'])->name('user-management-privilege');
Route::post('get-category-privileges',[UserManagementController::class,'getUserPrivileges'])->name('get-category-privileges');
Route::post('save-user-privileges',[UserManagementController::class,'saveUserPrivileges'])->name('save-user-privileges');

Route::get('user-management-create-account',[UserManagementController::class,'createAccountUser'])->name('user-management-create-account');
Route::post('get-user-email-process',[UserManagementController::class,'getAccountEmail']);
Route::post('create-account-process',[UserManagementController::class,'createAccount'])->name('create-account-process');

Route::get('user-management-list-create-account',[UserManagementController::class,'listAccountsView'])->name('user-management-list-create-account');
Route::any('user-management-get-accounts',[UserManagementController::class,'getUserAccountList'])->name('user-management-get-accounts');

Route::get('user-management-edit-user-account/{id}',[UserManagementController::class,'editUserAccountView'])->name('user-management-edit-user-account');
Route::post('user-management-edit-user-account-process/{id}',[UserManagementController::class,'editUserAccountProcess'])->name('user-management-edit-user-account-process');

Route::get('submenu/{id}',[UserManagementController::class,'getSubmenuItems'])->name('submenu');
Route::get('storemapping',[UserManagementController::class,'getstoremappingvoew'])->name('storemapping');
Route::post('map-store-process',[UserManagementController::class,'mapStore'])->name('map-store-process');
Route::get('/get-staff-stores/{staff_id}', [UserManagementController::class, 'getStaffStores'])->name('get-staff-stores');

/* Settings */
Route::get('department',[SettingsController::class,'getDepartmentView'])->name('department');
Route::post('add-department-process',[SettingsController::class,'addDepartment'])->name('add-department-process');
Route::get('department-id/{id}',[SettingsController::class,'getdepartmentID'])->name('department-id');
Route::post('edit-department-process',[SettingsController::class,'updateDepartment'])->name('edit-department-process');
Route::get('store',[SettingsController::class,'getStoreView'])->name('store');
Route::post('add-store-process',[SettingsController::class,'addStore'])->name('add-store-process');
Route::get('store-id/{id}',[SettingsController::class,'getstoreID'])->name('store-id');
Route::post('edit-store-process',[SettingsController::class,'updateStore'])->name('edit-store-process');
/* End Settings */

/* Stock Management */
Route::get('ItemCategory',[StockController::class,'getItemCatView'])->name('ItemCategory');
Route::post('add-itemcategory-process',[StockController::class,'addItemCategory'])->name('add-itemcategory-process');
Route::get('itemcat-id/{id}',[StockController::class,'getitemCatID'])->name('itemcat-id');

Route::post('edit-itemcategory-process',[StockController::class,'updateItemCategory'])->name('edit-itemcategory-process');

Route::get('unitOfmeasure',[StockController::class,'getunitOfmeasureView'])->name('unitOfmeasure');
Route::post('add-unitofmeasure-process',[StockController::class,'addUnitOfMeasure'])->name('add-unitofmeasure-process');
Route::get('unitofmeasure-id/{id}',[StockController::class,'getUnitofMeasureID'])->name('unitofmeasure-id');

Route::post('edit-unitofmeasure-process',[StockController::class,'updateUnitOfMeasure'])->name('edit-unitofmeasure-process');

Route::get('Item',[StockController::class,'getItemView'])->name('Item');
Route::post('add-item-process',[StockController::class,'addItem'])->name('add-item-process');
Route::post('update-item-process',[StockController::class,'updateItem'])->name('update-item-process');
Route::get('item-id/{id}',[StockController::class,'getItemID'])->name('item-id');

Route::get('reOrder',[StockController::class,'getreOrderView'])->name('reOrder');
Route::post('add-reorderlevel-process',[StockController::class,'addreorderlevel'])->name('add-reorderlevel-process');
Route::get('stockEntry',[StockController::class,'getstockEntryView'])->name('stockEntry');
Route::get('Supplier',[StaffController::class,'getSupplierView'])->name('Supplier');
Route::post('add-supplier-process',[StaffController::class,'addSupplier'])->name('add-supplier-process');
Route::get('supplier-id/{id}',[StaffController::class,'getSupplierID'])->name('supplier-id');
Route::post('update-supplier-process',[StaffController::class,'updateSupplier'])->name('update-supplier-process');
Route::post('add-stock-process',[StockController::class,'addStock'])->name('add-stock-process');
Route::get('stock-id/{id}',[StockController::class,'getStockID'])->name('stock-id');

Route::get('stockEntry/{id}/delete', [StockController::class, 'deleteStockItem']);
Route::post('edit-stock-process',[StockController::class,'updateStock'])->name('edit-stock-process');
Route::get('stockApproval',[StockController::class,'getstockApprovalView'])->name('stockApproval');
Route::get('/stockApproval/{id}', [StockController::class, 'ApproveStock'])->name('stock.stockApproval');
Route::get('/approve-all-stock', [StockController::class, 'approveAll'])
    ->name('stock.approveAll');
Route::get('pendingStock',[StockController::class,'getpendingStockView'])->name('pendingStock');
Route::get('approvedStock',[StockController::class,'getapprovedStockView'])->name('approvedStock');
Route::get('IssueItem',[StockController::class,'getIssueItemView'])->name('IssueItem');
Route::post('/get-batch-number', [StockController::class, 'getBatchNumber'])
    ->name('get.batch.number');
Route::post('add-itemissue-process',[StockController::class,'addItemIssue'])->name('add-itemissue-process');

Route::get('IssueItem/{id}/delete', [StockController::class, 'deleteStockIssue']);
Route::get('/approve-all-issues', [StockController::class, 'approveAllIssues'])
    ->name('stock.IssueapproveAll');

Route::get('IssueApproval',[StockController::class,'getIssueApproval'])->name('IssueApproval');
Route::post('IssueApproval',[StockController::class,'searchIssues'])->name('stock.search-issues');

Route::post('approveIssue-process', [StockController::class, 'ApproveIssueIndv'])->name('approveIssue-process');
Route::get('/stock/print/{invoice}', [StockController::class, 'printIssue'])
    ->name('stock.print');
Route::post('add-bulkupload-process', [StockController::class, 'addBulkupload'])->name('add-bulkupload-process');

Route::get('issue-item-id/{id}',[StockController::class,'getIssueItemID'])->name('issue-item-id');
Route::post('add-rejection-process', [StockController::class, 'addItemRejection'])->name('add-rejection-process');
 
/* End of Stock Management */

/* Reports */
Route::get('ItemReport',[ReportController::class,'getItemReportView'])->name('ItemReport');
Route::post('ItemReport',[ReportController::class,'searchStockReport'])->name('report.stock-report');
Route::get('ReceivedStocks',[ReportController::class,'getReceivedStocksView'])->name('ReceivedStocks');
Route::post('ReceivedStocks',[ReportController::class,'searchReceivedStockReport'])->name('report.stockreceived-report');
Route::get('/received-stock-print/{department}',
    [ReportController::class, 'printReceivedStockReport'])
    ->middleware('auth')
    ->name('report.received-stock-print');
Route::get('ReceivedStockByDate',[ReportController::class,'getReceivedStockByDateView'])->name('ReceivedStockByDate');
Route::post('ReceivedStockByDate',[ReportController::class,'stockreceivedDateInter'])->name('report.stockreceivedDateInter-report');
Route::get('/received-stock-date-print',
    [ReportController::class, 'printReceivedStockDateReport'])
    ->middleware('auth')  ->name('report.received-stock-date-print');

Route::get('searchByItem',[ReportController::class,'getsearchByItemView'])->name('searchByItem');
Route::post('searchByItem',[ReportController::class,'searchReceivedStockByItemReport'])->name('report.stockreceivedbyitem-report');
Route::get('/received-stock-byitem-print',[ReportController::class, 'printReceivedStockByItemReport'])->middleware('auth')  ->name('report.received-stock-byitem-print');
Route::get('searchByItemDate',[ReportController::class,'getsearchByItemDateView'])->name('searchByItemDate');
Route::post('searchByItemDate',[ReportController::class,'searchReceivedStockByItemDateReport'])->name('report.stockreceivedbyitemDate-report');
Route::get('/received-stock-byitemDate-print',[ReportController::class, 'printReceivedStockByItemDateReport'])->middleware('auth')  ->name('report.received-stock-byitemDate-print');

Route::get('IssuedItemsReport',[ReportController::class,'getIssuedItemsReportView'])->name('IssuedItemsReport');
Route::get('ReOrderLevelReport',[ReportController::class,'getIssuedItemsReportView'])->name('ReOrderLevelReport');

/* End of Report */