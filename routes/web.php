<?php


use App\Http\Controllers\Asset\AssetController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication\AuthenticationController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Issues\IssueController;
use App\Http\Controllers\notification\NotificationController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\Report\ReportDetailsController;
use App\Http\Controllers\Requisition\RequisitionController;
use App\Http\Controllers\UserManagement\UserManagementController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Stock\StockController;
use App\Http\Controllers\Stock\StockReceiptController;
use App\Http\Controllers\Stock\SatelliteIssueController;
use App\Http\Controllers\Stock\ReverseEntryController;
 

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
Route::get('choose-store',[AuthenticationController::class,'getChooseStoreView'])->name('choose-store')->middleware('auth');
Route::post('select-store-process',[AuthenticationController::class,'selectStoreProcess'])->name('select-store-process')->middleware('auth');
Route::get('switch-store',[AuthenticationController::class,'switchStore'])->name('switch-store')->middleware('auth');
Route::post('logout-authentication-process',[DashboardController::class,'logoutAuthenticationProcess'])->name('logout-authentication-process');

/** Staff Manager */
Route::get('create-staff',[StaffController::class,'addStaffView'])->name('create-staff');
Route::post('add-staff-process',[StaffController::class,'addStaff'])->name('add-staff-process');
Route::get('list-staff',[StaffController::class,'getStaffListView'])->name('list-staff');
Route::get('edit-staff/{staff_id}',[StaffController::class,'getEditStaffView'])->name('edit-staff');
Route::get('staff-id/{id}',[StaffController::class,'getStaffID'])->name('staff-id');
Route::post('edit-staff-process/{staff_id}',[StaffController::class,'editStaff'])->name('edit-staff-process');
Route::post('delete-staff-process/{staff_id}',[StaffController::class,'deleteStaff'])->name('delete-staff-process');
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
Route::get('ward',[SettingsController::class,'getWardView'])->name('ward')->middleware(['auth', 'active.store']);
Route::post('add-ward-process',[SettingsController::class,'addWard'])->name('add-ward-process')->middleware(['auth', 'active.store']);
Route::get('ward-id/{id}',[SettingsController::class,'getWardID'])->name('ward-id')->middleware(['auth', 'active.store']);
Route::post('edit-ward-process',[SettingsController::class,'updateWard'])->name('edit-ward-process')->middleware(['auth', 'active.store']);
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
Route::post('delete-itemcategory-process/{id}',[StockController::class,'deleteItemCategory'])->name('delete-itemcategory-process');

Route::get('unitOfmeasure',[StockController::class,'getunitOfmeasureView'])->name('unitOfmeasure');
Route::post('add-unitofmeasure-process',[StockController::class,'addUnitOfMeasure'])->name('add-unitofmeasure-process');
Route::get('unitofmeasure-id/{id}',[StockController::class,'getUnitofMeasureID'])->name('unitofmeasure-id');

Route::post('edit-unitofmeasure-process',[StockController::class,'updateUnitOfMeasure'])->name('edit-unitofmeasure-process');
Route::post('delete-unitofmeasure-process/{id}',[StockController::class,'deleteUnitOfMeasure'])->name('delete-unitofmeasure-process');

Route::get('Item',[StockController::class,'getItemView'])->name('Item')->middleware(['auth', 'active.store']);
Route::post('add-item-process',[StockController::class,'addItem'])->name('add-item-process')->middleware(['auth', 'active.store']);
Route::post('update-item-process',[StockController::class,'updateItem'])->name('update-item-process')->middleware(['auth', 'active.store']);
Route::get('item-id/{id}',[StockController::class,'getItemID'])->name('item-id')->middleware(['auth', 'active.store']);
Route::post('delete-item-process/{id}',[StockController::class,'deleteItem'])->name('delete-item-process')->middleware(['auth', 'active.store']);

Route::get('reOrder',[StockController::class,'getreOrderView'])->name('reOrder');
Route::post('add-reorderlevel-process',[StockController::class,'addreorderlevel'])->name('add-reorderlevel-process');
Route::get('stockEntry',[StockController::class,'getstockEntryView'])->name('stockEntry');
Route::get('Supplier',[StaffController::class,'getSupplierView'])->name('Supplier');
Route::post('add-supplier-process',[StaffController::class,'addSupplier'])->name('add-supplier-process');
Route::get('supplier-id/{id}',[StaffController::class,'getSupplierID'])->name('supplier-id');
Route::post('update-supplier-process',[StaffController::class,'updateSupplier'])->name('update-supplier-process');
Route::post('delete-supplier-process/{id}',[StaffController::class,'deleteSupplier'])->name('delete-supplier-process');
Route::post('add-stock-process',[StockController::class,'addStock'])->name('add-stock-process');
Route::get('stock-id/{id}',[StockController::class,'getStockID'])->name('stock-id');

Route::get('stockEntry/{id}/delete', [StockController::class, 'deleteStockItem']);
Route::post('edit-stock-process',[StockController::class,'updateStock'])->name('edit-stock-process');
Route::get('stockApproval',[StockController::class,'getstockApprovalView'])->name('stockApproval');
Route::get('/stockApproval/{id}', [StockController::class, 'ApproveStock'])->name('stock.stockApproval');
Route::post('/stock/reject/{id}', [StockController::class, 'rejectStock'])->name('stock.reject');
Route::post('/stock/reject-all/{store_id}', [StockController::class, 'rejectAll'])->name('stock.rejectAll');
 

    Route::get('/approve-all-stock/{store_id}', [StockController::class,'approveAll'])
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
Route::post('/get-item-uom', [StockController::class, 'getItemUom'])->name('get.item.uom');



Route::post('approveIssue-process', [StockController::class, 'ApproveIssueIndv'])->name('approveIssue-process');
Route::get('/stock/prints/{invoice}', [StockController::class, 'printIssue'])
    ->name('stock.print');
Route::post('add-bulkupload-process', [StockController::class, 'addBulkupload'])->name('add-bulkupload-process');

Route::get('issue-item-id/{id}',[StockController::class,'getIssueItemID'])->name('issue-item-id');
Route::post('add-rejection-process', [StockController::class, 'addItemRejection'])->name('add-rejection-process');
Route::get('MyRequest',[StockController::class,'getMyRequestView'])->name('MyRequest');
Route::get('viewStockEntry/{store_id}',[StockController::class,'getviewStockEntry'])->name('viewStockEntry');

Route::get('ReceiveStock', [StockReceiptController::class, 'index'])->name('ReceiveStock')->middleware(['auth', 'active.store']);
Route::get('viewReceiveStock/{requisition_no}', [StockReceiptController::class, 'show'])->name('viewReceiveStock')->middleware(['auth', 'active.store']);
Route::post('acceptReceiveStock/{requisition_no}', [StockReceiptController::class, 'accept'])->name('acceptReceiveStock')->middleware(['auth', 'active.store']);

Route::get('IssueItemSatellite', [SatelliteIssueController::class, 'index'])->name('IssueItemSatellite')->middleware(['auth', 'active.store']);
Route::post('add-satellite-issue-process', [SatelliteIssueController::class, 'add'])->name('add-satellite-issue-process')->middleware(['auth', 'active.store']);
Route::get('IssueItemSatellite/{id}/delete', [SatelliteIssueController::class, 'destroy'])->name('satellite-issue.delete')->middleware(['auth', 'active.store']);
Route::get('/submit-satellite-issue', [SatelliteIssueController::class, 'submit'])->name('satellite-issue.submit')->middleware(['auth', 'active.store']);
Route::get('/issue-satellite-batch/{issue_no}', [SatelliteIssueController::class, 'issueBatch'])->name('satellite-issue.issue-batch')->middleware(['auth', 'active.store']);
Route::get('/satellite-issue/print/{issue_no}', [SatelliteIssueController::class, 'printIssueSlip'])->name('satellite-issue.print')->middleware(['auth', 'active.store']);
Route::post('/get-satellite-batch-number', [SatelliteIssueController::class, 'getBatchNumber'])->name('get.satellite.batch.number')->middleware(['auth', 'active.store']);

Route::get('reverseEntry', [ReverseEntryController::class, 'index'])->name('reverseEntry')->middleware(['auth', 'active.store']);
Route::post('add-reverse-entry-process', [ReverseEntryController::class, 'store'])->name('add-reverse-entry-process')->middleware(['auth', 'active.store']);
Route::get('reverse-entry/batch', [ReverseEntryController::class, 'batchLookup'])->name('reverse-entry.batch')->middleware(['auth', 'active.store']);
Route::get('reverse-entry-id/{id}', [ReverseEntryController::class, 'getBatchById'])->name('reverse-entry-id')->middleware(['auth', 'active.store']);
Route::get('reverseEntryApproval', [ReverseEntryController::class, 'approvalIndex'])->name('reverseEntryApproval')->middleware(['auth', 'active.store']);
Route::post('reverse-entry/{reversal}/approve', [ReverseEntryController::class, 'approve'])->name('reverse-entry.approve')->middleware(['auth', 'active.store']);
Route::post('reverse-entry/{reversal}/reject', [ReverseEntryController::class, 'reject'])->name('reverse-entry.reject')->middleware(['auth', 'active.store']);

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
Route::get('ReOrderLevelReport',[ReportController::class,'getReorderLevelReportView'])->name('ReOrderLevelReport');
Route::post('ReOrderLevelReport',[ReportController::class,'searchStockLevelReport'])->name('report.stockreorderlevel-report');
Route::get('/reorderlevel-stock-print/{department}', [ReportController::class, 'printReoderlevelStockReport']) ->middleware('auth')->name('report.reorderlevel-stock-print');

Route::post('IssuedItemsReport',[ReportController::class,'getIssuedItemsReportView'])->name('IssuedItemsReport');
Route::post('searchIssueItemByStore',[ReportController::class,'searchIssuedStoreReport'])->name('report.issueditem-report');
Route::get('/issueditem-stock-report/{department}', [ReportController::class, 'printIssuedItemReport']) ->middleware('auth')->name('report.issueditem-stock-print');
 
Route::get('searchIssueItemByStore',[ReportController::class,'getsearchIssueItemByStoreView'])->name('searchIssueItemByStore');

Route::get('searchByIssueItem',[ReportController::class,'getsearchByIssueItemView'])->name('searchByIssueItem');
Route::post('searchByIssueItem',[ReportController::class,'searchIssuedItemReport'])->name('report.searchbyIssueItemRep-report');
Route::get('/issueditem-print/{department}', [ReportController::class, 'printIssuedByItemReport']) ->middleware('auth')->name('report.issueditem-print');

Route::get('searchByIssuedDate',[ReportController::class,'getsearchByIssuedDateView'])->name('searchByIssuedDate');
Route::post('searchByIssuedDate',[ReportController::class,'searchByIssuedDate'])->name('report.searchByIssuedDate-report');
Route::get('/issued-item-date-print',
    [ReportController::class, 'printIssuedItemDateReport'])
    ->middleware('auth')  ->name('report.issued-item-date-print');

Route::get('searchIssuedItemByDateIntev',[ReportController::class,'getsearchIssuedItemByDateIntevView'])->name('searchIssuedItemByDateIntev');
Route::post('searchIssuedItemByDateIntev',[ReportController::class,'searchIssuedItemByDateIntev'])->name('report.searchIssuedItemByDateIntev-report');
Route::get('/issued-item-byitemDate-print',[ReportController::class, 'printIssuedItemDateIntervalReport'])->middleware('auth')  ->name('report.issued-item-byitemDate-print');
Route::get('CommodityReport',[ReportDetailsController::class,'getCommodityReportView'])->name('CommodityReport'); 
Route::post('CommodityReport',[ReportDetailsController::class,'searchCommodityReport'])->name('report.searchCommodity-report');
Route::get('/commodityreport-print',[ReportDetailsController::class, 'printCommoditySummaryReport'])->middleware('auth')  ->name('report.commodityreport-print');

Route::get('DetailedCommodityReport',[ReportDetailsController::class,'getDetailedCommodityReportView'])->name('DetailedCommodityReport'); 
Route::post('DetailedCommodityReport',[ReportDetailsController::class,'searchCommodityDetailReport'])->name('report.searchCommodityDetails-report');
Route::get('/commoditydetailedreport-print',[ReportDetailsController::class, 'printCommodityDetailReport'])->middleware('auth')  ->name('report.commoditydetailedreport-print');
Route::get('/summary-report-print',[ReportDetailsController::class, 'printSummaryReport'])->middleware('auth')  ->name('report.summary-report-print');

Route::get('searchIssuedItemByDepartment',[ReportController::class,'searchIssueItemByDepartmentView'])->name('searchIssuedItemByDepartment');
Route::post('searchIssuedItemByDepartment',[ReportController::class,'searchIssuedItemByDeprtReport'])->name('report.issueddepartitem-report');
Route::get('/issueditem-by-department-stock-report/{department}', [ReportController::class, 'printIssuedItemByDepartReport']) ->middleware('auth')->name('report.issueditem-by-department-stock-print');


Route::get('searchByIssueItemDepartment',[ReportController::class,'getsearchByIssueItemDepartmentView'])->name('searchByIssueItemDepartment');
Route::post('searchByIssueItemDepartment',[ReportController::class,'searchIssuedItemDepartReport'])->name('report.searchbyIssueItemDepartment-report');
Route::get('/issueditem-department-print/{department}', [ReportController::class, 'printIssuedByItemDepartReport']) ->middleware('auth')->name('report.issueditem-department-print');

Route::get('searchByIssuedDepartmentDate',[ReportController::class,'getsearchByIssuedDateDepartView'])->name('searchByIssuedDepartmentDate');
Route::post('searchByIssuedDepartmentDate',[ReportController::class,'searchByIssuedDepartDate'])->name('report.searchByIssuedDepartmentDate-report');
Route::get('/issued-item-date-department-print',
    [ReportController::class, 'printIssuedItemDateDepartReport'])
    ->middleware('auth')  ->name('report.issued-item-date-department-print');

Route::get('searchIssuedItemByDepartmentDateIntev',[ReportController::class,'getsearchIssuedItemByDepartDateIntevView'])->name('searchIssuedItemByDepartmentDateIntev');
Route::post('searchIssuedItemByDepartmentDateIntev',[ReportController::class,'searchIssuedItemByDepartDateIntev'])->name('report.searchIssuedItemBydepartmentDateIntev-report');
Route::get('/issued-item-byitemDepartDate-print',[ReportController::class, 'printIssuedItemDateIntervalDepartReport'])->middleware('auth')  ->name('report.issued-item-byitemDepartDate-print');



/* End of Report */


/* Requisition */

Route::get('Requisition',[RequisitionController::class,'getRequisitionView'])->name('Requisition'); 
Route::post('add-request-process',[RequisitionController::class,'addRequest'])->name('add-request-process');
Route::get('Requisition/{id}/delete', [RequisitionController::class, 'deleteitemRequest']);
 
Route::get('/submit-all-requests', [RequisitionController::class, 'submitRequest'])
    ->name('requisition.SubmitRequest');
Route::get('MyRequest',[RequisitionController::class,'getMyRequestView'])->name('MyRequest'); 
Route::get('ApproveRequest',[RequisitionController::class,'getApproveRequestView'])->name('ApproveRequest'); 
Route::get('viewRequest/{requisition_no}',[RequisitionController::class,'getviewRequest'])->name('viewRequest');
Route::get('request-item-id/{id}',[RequisitionController::class,'getrequesttemID'])->name('request-item-id');
Route::post('add-reject-request-process', [RequisitionController::class, 'addItemRejectRequest'])->name('add-reject-request-process');
Route::post('approve-request-process', [RequisitionController::class, 'addApproveRequest'])->name('approve-request-process');

Route::get('viewIssues/{requisition_no}',[RequisitionController::class,'getIssuedItems'])->name('viewIssues');
Route::post('approve-issues-process', [RequisitionController::class, 'addApproveIssues'])->name('approve-issues-process');


/* End of Requisition */

/* Issues */
Route::get('viewStoreRequest/{requisition_no}',[IssueController::class,'getviewStoreRequest'])->name('viewStoreRequest');
Route::post('issue-request-process', [IssueController::class, 'addIssueRequest'])->name('issue-request-process');
Route::get('PickList',[RequisitionController::class,'getPickListView'])->name('PickList')->middleware(['auth', 'active.store']);
Route::get('viewPickUp/{requisition_no}',[RequisitionController::class,'getviewPickList'])->name('viewPickUp')->middleware(['auth', 'active.store']);
Route::get('/stock/print/{invoice}', [RequisitionController::class, 'printPickList'])
    ->name('requisition.print')->middleware(['auth', 'active.store']);

Route::get('Return',[RequisitionController::class,'getReturnView'])->name('Return'); 
Route::get('return-item-id/{id}',[RequisitionController::class,'getreturnItemID'])->name('return-item-id');
Route::post('add-retrun-item-process', [RequisitionController::class, 'addReturn'])->name('add-retrun-item-process');
Route::get('ReturnApproval',[RequisitionController::class,'getReturnApprovalView'])->name('ReturnApproval'); 
Route::post('add-retrun-item-approval-process', [RequisitionController::class, 'addReturnApproval'])->name('add-retrun-item-approval-process');
Route::get('return-item-approval-id/{id}',[RequisitionController::class,'getreturnItemApprovalID'])->name('return-item-approval-id');

// routes/web.php
Route::get('check-notifications', [NotificationController::class, 'checkNew'])->name('check-notifications')->middleware('auth');
Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index')->middleware('auth');
Route::post('notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read')->middleware('auth');
Route::post('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all')->middleware('auth');


/* End of Issues*/