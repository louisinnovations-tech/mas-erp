<?php

use App\Http\Controllers\AamarpayController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdvocateController;
use App\Http\Controllers\AiTemplateController;
use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\BenchController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\CauseController;
use App\Http\Controllers\CountryStateCityController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\HighCourtController;
use App\Http\Controllers\ToDoController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\TimeSheetController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DoctypeController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\PaystackPaymentController;
use App\Http\Controllers\FlutterwavePaymentController;
use App\Http\Controllers\RazorpayPaymentController;
use App\Http\Controllers\MercadoPaymentController;
use App\Http\Controllers\PaytmPaymentController;
use App\Http\Controllers\MolliePaymentController;
use App\Http\Controllers\SkrillPaymentController;
use App\Http\Controllers\CoingatePaymentController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BankTransferController;
use App\Http\Controllers\BenefitPaymentController;
use App\Http\Controllers\CashfreeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ConversionController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\DealStageController;
use App\Http\Controllers\HearingController;
use App\Http\Controllers\HearingTypeController;
use App\Http\Controllers\IyziPayController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\PayfastController;
use App\Http\Controllers\PaymentWallController;
use App\Http\Controllers\PaytabController;
use App\Http\Controllers\PaytrController;
use App\Http\Controllers\SspayController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\ToyyibpayController;
use App\Http\Controllers\UserlogController;
use App\Http\Controllers\XenditPaymentController;
use App\Http\Controllers\YooKassaController;
use App\Http\Controllers\DocSubTypeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KnowledgebaseCategoryController;
use App\Http\Controllers\KnowledgeController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadStageController;
use App\Http\Controllers\MotionController;
use App\Http\Controllers\OperatinghoursController;
use App\Http\Controllers\PayHereController;
use App\Http\Controllers\PipelineController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\SLAPoliciyController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketCustomFieldController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\ZoommeetingController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\DocumentUploadController;
use App\Http\Controllers\CompanyPolicyController;
use App\Http\Controllers\AwardController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\ResignationController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\WarningController;
use App\Http\Controllers\TerminationController;
use App\Http\Controllers\IndicatorController;
use App\Http\Controllers\AppraisalController;
use App\Http\Controllers\GoalTrackingController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\AwardTypeController;
use App\Http\Controllers\TerminationTypeController;
use App\Http\Controllers\TrainingTypeController;
use App\Http\Controllers\PerformanceTypeController;
use App\Http\Controllers\CompetenciesController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\GoalTypeController;
use App\Http\Controllers\PracticeAreaController;
use App\Http\Controllers\SalaryTypeController;
use App\Models\User;


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


require __DIR__.'/auth.php';

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('optimize:clear');

    return redirect()->back()->with('success', __('Clear Cache successfully.'));
});

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::group(['middleware'=>['auth','XSS','verified']], function(){
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('permissions', PermissionController::class);
    Route::resource('roles', RoleController::class);

    Route::resource('users', UserController::class);

    Route::resource('employee', EmployeeController::class);

    Route::resource('pipeline', PipelineController::class);

    Route::get('users-list', [UserController::class, 'userList'])->name('users.list');
    Route::post('users/{id}/change-password',[UserController::class,'changeMemberPassword'])->name('member.change.password');
    Route::any('company-reset-password/{id}', [UserController::class, 'companyPassword'])->name('company.reset');
    Route::any('users/verify/{id}', [UserController::class, 'verify'])->name('users.verify')->middleware(['auth', 'XSS']);
    Route::get('users/detail/{id}', [UserController::class, 'detail'])->name('users.detail')->middleware(['auth', 'XSS']);

    Route::resource('teams', TeamController::class);

    Route::resource('groups', GroupController::class);

    Route::resource('advocate', AdvocateController::class);
    Route::get('/advocate/contacts/{id}', [AdvocateController::class, 'contacts'])->name('advocate.contacts');
    Route::get('/advocate/bills/{id}', [AdvocateController::class, 'bills'])->name('advocate.bill');
    Route::get('/get-country', [CountryStateCityController::class, 'getCountry'])->name('get.country');
    Route::post('/get-state', [CountryStateCityController::class, 'getState'])->name('get.state');
    Route::post('/get-city', [CountryStateCityController::class, 'getCity'])->name('get.city');
    Route::get('/get-all-city', [CountryStateCityController::class, 'getAllState'])->name('get.all.state');

    Route::get('advocates/export', [AdvocateController::class, 'exportFile'])->name('advocates.export');

    Route::get('/advocates-fileimport', [AdvocateController::class, 'fileImport'])->name('advocates.file.import');
    Route::post('import/advocates', [AdvocateController::class, 'import'])->name('advocates.import');

    Route::get('bills/export', [BillController::class, 'exportFile'])->name('bills.export');

    Route::get('timesheets/export', [TimeSheetController::class, 'exportFile'])->name('timesheets.export');

    Route::get('expenses/export', [ExpenseController::class, 'exportFile'])->name('expenses.export');

    Route::get('feereceive/export', [FeeController::class, 'exportFile'])->name('feereceive.export');

    Route::resource('courts', CourtController::class);

    Route::resource('highcourts', HighCourtController::class);

    Route::resource('bench', BenchController::class);

    Route::resource('cause', CauseController::class);
    Route::post('/cause/get-highcourts', [CauseController::class, 'getHighCourt'])->name('get.highcourt');
    Route::post('/cause/get-bench', [CauseController::class, 'getBench'])->name('get.bench');

    Route::resource('cases', CaseController::class);
    Route::get('cases/journey/{id}', [CaseController::class, 'journey'])->name('cases.journey');
    Route::post('cases/journey-update/{id}', [CaseController::class, 'updateJourney'])->name('update.journey');
    Route::get('/cases/docs-delete/{id}/{key}', [CaseController::class, 'casesDocsDestroy'])->name('cases.docs.destroy');
    Route::get('import/case/file', [CaseController::class, 'importFile'])->name('case.file.import');
    Route::post('import/case', [CaseController::class, 'import'])->name('case.import');

    Route::get('export/case/file', [CaseController::class, 'exportFile'])->name('cases.export');

    Route::resource('to-do', ToDoController::class);
    Route::get('to-do/status/{id}', [ToDoController::class, 'status'])->name('to-do.status');
    Route::PUT('to-do/status-update/{id}', [ToDoController::class, 'statusUpdate'])->name('to-do.status.update');

    Route::get('bills/addpayment/{bill_id}', [BillController::class,'paymentcreate'])->name('create.payment');
    Route::POST('bills/storepayment/{bill_id}', [BillController::class,'paymentstore'])->name('payment.store');


    Route::resource('taxs', TaxController::class);
    Route::post('taxs/get-tax/', [TaxController::class, 'getTax'])->name('get.tax');

    Route::resource('casediary', DiaryController::class);

    Route::resource('timesheet', TimeSheetController::class);

    Route::resource('expenses', ExpenseController::class);

    Route::resource('fee-receive', FeeController::class);

    Route::resource('calendar', CalendarController::class);

    Route::resource('documents', DocumentController::class);

    Route::resource('doctype', DoctypeController::class);

    Route::resource('doctsubype', DocSubTypeController::class);
    Route::post('doctsubype/getDocSubType', [DocSubTypeController::class, 'getDocSubType'])->name('get.docSubType');

    Route::resource('motions', MotionController::class);

    Route::resource('settings', SettingController::class);

    Route::post('storage-settings',[SettingController::class,'storageSettingStore'])->name('storage.setting.store');


    Route::get('change-language/{lang}', [LanguageController::class, 'changeLanquage'])->name('change.language');
    Route::get('manage-language/{lang}', [LanguageController::class, 'manageLanguage'])->name('manage.language');
    Route::post('store-language-data/{lang}', [LanguageController::class, 'storeLanguageData'])->name('store.language.data');
    Route::get('create-language', [LanguageController::class, 'createLanguage'])->name('create.language');
    Route::post('store-language', [LanguageController::class, 'storeLanguage'])->name('store.language');
    Route::delete('destroy-language/{lang}', [LanguageController::class, 'destroyLang'])->name('destroy.language');
    Route::post('disable-language',[LanguageController::class,'disableLang'])->name('disablelanguage')->middleware(['auth','XSS']);

    Route::post('cookie-setting', [SettingController::class, 'saveCookieSettings'])->name('cookie.setting');
    Route::post('email-settings', [SettingController::class,'saveCompanyEmailSettings'])->name('email.settings');
    Route::post('company-email-settings', [SettingController::class,'saveCompanyEmailSettings'])->name('company.email.settings');
    Route::any('test', [SettingController::class,'testMail'])->name('test.mail');
    Route::post('test-mail', [SettingController::class,'testSendMail'])->name('test.send.mail');
    Route::post('setting/seo', [SettingController::class, 'SeoSettings'])->name('seo.settings');




    Route::post('recaptcha-settings', [SettingController::class, 'recaptchaSettingStore'])->name('recaptcha.settings.store');

    Route::get('system-settings', [SettingController::class, 'adminSettings'])->name('admin.settings');
    Route::post('business-setting', [SettingController::class,'saveBusinessSettings'])->name('business.setting');

    Route::resource('coupons', CouponController::class);

    Route::get('/orders', [StripePaymentController::class, 'index'])->name('order.index');
    Route::get('/apply-coupon', [CouponController::class,'applyCoupon'])->name('apply.coupon');

    Route::post('/stripe', [StripePaymentController::class, 'stripePost'])->name('stripe.post');


    Route::get('orders/show/{id}', [BankTransferController::class, 'show'])->name('order.show');
    Route::delete('/bank_transfer/{order}/', [BankTransferController::class, 'destroy'])->name('bank_transfer.destroy');
    Route::any('order_approve/{id}', [BankTransferController::class, 'orderapprove'])->name('order.approve');
    Route::any('order_reject/{id}', [BankTransferController::class, 'orderreject'])->name('order.reject');

    Route::post('pusher-setting', [SettingController::class, 'savePusherSettings'])->name('pusher.setting');
    Route::get('/advocate/view/{id}', [AdvocateController::class, 'view'])->name('advocate.view');

    Route::post('setting/google-calender', [SettingController::class, 'saveGoogleCalenderSettings'])->name('google.calender.settings');
    Route::post('data/get_all_data', [CalendarController::class, 'get_call_data'])->name('call.get_call_data');

    Route::resource('userlog',UserlogController::class);
    Route::delete('/userlog/{id}/', [UserlogController::class, 'destroy'])->name('userlog.destroy')->middleware(['auth','XSS']);
    Route::get('userlog-view/{id}/', [UserlogController::class, 'view'])->name('userlog.view')->middleware(['auth','XSS']);

    Route::resource('country',CountryController::class);
    Route::resource('state',StateController::class);
    Route::resource('city',CityController::class);

    Route::resource('hearingType',HearingTypeController::class);
    Route::get('/hearing/{case_id}', [HearingController::class, 'create'])->name('hearings.create');
    Route::resource('hearing',HearingController::class);
    Route::get('import/hearing/file/{case_id}', [HearingController::class, 'importFile'])->name('hearing.file.import');
    Route::post('import/hearing', [HearingController::class, 'import'])->name('hearing.import');

    Route::resource('appointments',AppointmentsController::class);

    Route::resource('client', ClientController::class);
    Route::get('client-list', [ClientController::class, 'userList'])->name('client.list');

    Route::get('/client-fileimport', [ClientController::class, 'fileImport'])->name('clients.file.import');
    Route::post('import/client', [ClientController::class, 'import'])->name('client.import');

    Route::get('clients-export', [ClientController::class, 'exportFile'])->name('clients.export');


    Route::post('leadStage/order', [LeadStageController::class, 'order'])->name('leadStage.order');
    Route::resource('leadStage', LeadStageController::class);

    Route::post('dealStage/order', [DealStageController::class, 'order'])->name('dealStage.order');
    Route::post('dealStage/json', [DealStageController::class, 'json'])->name('dealStage.json');
    Route::resource('dealStage', DealStageController::class);

    Route::resource('dealStage', DealStageController::class);
    Route::resource('source', SourceController::class);
    Route::resource('label', LabelController::class);
    Route::get('lead/grid', [LeadController::class, 'grid'])->name('lead.grid');
    Route::post('lead/json', [LeadController::class, 'json'])->name('lead.json');
    Route::post('lead/order', [LeadController::class, 'order'])->name('lead.order');
    Route::get('lead/{id}/users', [LeadController::class, 'userEdit'])->name('lead.users.edit');
    Route::post('lead/{id}/users', [LeadController::class, 'userUpdate'])->name('lead.users.update');
    Route::delete('lead/{id}/users/{uid}', [LeadController::class, 'userDestroy'])->name('lead.users.destroy');

    Route::get('lead/{id}/items', [LeadController::class, 'productEdit'])->name('lead.items.edit');
    Route::post('lead/{id}/items', [LeadController::class, 'productUpdate'])->name('lead.items.update');
    Route::delete('lead/{id}/items/{uid}', [LeadController::class, 'productDestroy'])->name('lead.items.destroy');

    Route::post('lead/{id}/file', [LeadController::class, 'fileUpload'])->name('lead.file.upload');
    Route::get('lead/{id}/file/{fid}', [LeadController::class, 'fileUpload'])->name('lead.file.download');
    Route::delete('lead/{id}/file/delete/{fid}', [LeadController::class, 'fileDelete'])->name('lead.file.delete');

    Route::get('lead/{id}/sources', [LeadController::class, 'sourceEdit'])->name('lead.sources.edit');
    Route::post('lead/{id}/sources', [LeadController::class, 'sourceUpdate'])->name('lead.sources.update');
    Route::delete('lead/{id}/sources/{uid}', [LeadController::class, 'sourceDestroy'])->name('lead.sources.destroy');

    Route::get('lead/{id}/discussions', [LeadController::class, 'discussionCreate'])->name('lead.discussions.create');
    Route::post('lead/{id}/discussions', [LeadController::class, 'discussionStore'])->name('lead.discussion.store');

    Route::get('lead/{id}/call', [LeadController::class, 'callCreate'])->name('lead.call.create');
    Route::post('lead/{id}/call', [LeadController::class, 'callStore'])->name('lead.call.store');
    Route::get('lead/{id}/call/{cid}/edit', [LeadController::class, 'callEdit'])->name('lead.call.edit');
    Route::post('lead/{id}/call/{cid}', [LeadController::class, 'callUpdate'])->name('lead.call.update');
    Route::delete('lead/{id}/call/{cid}', [LeadController::class, 'callDestroy'])->name('lead.call.destroy');

    Route::get('lead/{id}/email', [LeadController::class, 'emailCreate'])->name('lead.email.create');
    Route::post('lead/{id}/email', [LeadController::class, 'emailStore'])->name('lead.email.store');

    Route::get('lead/{id}/label', [LeadController::class, 'labels'])->name('lead.label');
    Route::post('lead/{id}/label', [LeadController::class, 'labelStore'])->name('lead.label.store');

    Route::get('lead/{id}/show_convert', [LeadController::class, 'showConvertToDeal'])->name('lead.convert.deal');
    Route::post('lead/{id}/convert', [LeadController::class, 'convertToDeal'])->name('lead.convert.to.deal');

    Route::get('lead/{id}/show_convert', [LeadController::class, 'showConvertToDeal'])->name('lead.convert.deal');
    Route::post('lead/{id}/convert', [LeadController::class, 'convertToDeal'])->name('lead.convert.to.deal');

    Route::post('lead/change-pipeline', [LeadController::class, 'changePipeline'])->name('lead.change.pipeline');
    Route::resource('lead', LeadController::class);
    Route::post('lead/{id}/note', [LeadController::class, 'noteStore'])->name('lead.note.store');
    Route::post('deal/order', [DealController::class, 'order'])->name('deal.order');
    Route::get('deal/{id}/users', [DealController::class, 'userEdit'])->name('deal.users.edit');
    Route::post('deal/{id}/users', [DealController::class, 'userUpdate'])->name('deal.users.update');
    Route::delete('deal/{id}/users/{uid}', [DealController::class, 'userDestroy'])->name('deal.users.destroy');

    Route::post('deal/{id}/update', [DealController::class, 'Update'])->name('deal.update');


    Route::get('deal/{id}/items', [DealController::class, 'productEdit'])->name('deal.items.edit');
    Route::post('deal/{id}/items', [DealController::class, 'productUpdate'])->name('deal.items.update');
    Route::delete('deal/{id}/items/{uid}', [DealController::class, 'productDestroy'])->name('deal.items.destroy');

    Route::post('deal/{id}/file', [DealController::class, 'fileUpload'])->name('deal.file.upload');
    Route::get('deal/{id}/file/{fid}', [DealController::class, 'fileDownload'])->name('deal.file.download');
    Route::delete('deal/{id}/file/delete/{fid}', [DealController::class, 'fileDelete'])->name('deal.file.delete');



    Route::get('deal/{id}/task', [DealController::class, 'taskCreate'])->name('deal.tasks.create');
    Route::post('deal/{id}/task', [DealController::class, 'taskStore'])->name('deal.tasks.store');
    Route::get('deal/{id}/task/{tid}/show', [DealController::class, 'taskShow'])->name('deal.tasks.show');
    Route::get('deal/{id}/task/{tid}/edit', [DealController::class, 'taskEdit'])->name('deal.tasks.edit');
    Route::post('deal/{id}/task/{tid}', [DealController::class, 'taskUpdate'])->name('deal.tasks.update');
    Route::post('deal/{id}/task_status/{tid}', [DealController::class, 'taskUpdateStatus'])->name('deal.tasks.update_status');
    Route::delete('deal/{id}/task/{tid}', [DealController::class, 'taskDestroy'])->name('deal.tasks.destroy');

    Route::get('deal/{id}/products', [DealController::class, 'productEdit'])->name('deal.products.edit');
    Route::post('deal/{id}/products', [DealController::class, 'productUpdate'])->name('deal.products.update');
    Route::delete('deal/{id}/products/{uid}', [DealController::class, 'productDestroy'])->name('deal.products.destroy');

    Route::get('deal/{id}/sources', [DealController::class, 'sourceEdit'])->name('deal.sources.edit');
    Route::post('deal/{id}/sources', [DealController::class, 'sourceUpdate'])->name('deal.sources.update');
    Route::delete('deal/{id}/sources/{uid}', [DealController::class, 'sourceDestroy'])->name('deal.sources.destroy');



    Route::get('deal/{id}/discussions', [DealController::class, 'discussionCreate'])->name('deal.discussions.create');
    Route::post('deal/{id}/discussions', [DealController::class, 'discussionStore'])->name('deal.discussion.store');


    Route::get('deal/{id}/call', [DealController::class, 'callCreate'])->name('deal.call.create');
    Route::post('deal/{id}/call', [DealController::class, 'callStore'])->name('deal.call.store');
    Route::get('deal/{id}/call/{cid}/edit', [DealController::class, 'callEdit'])->name('deal.call.edit');
    Route::post('deal/{id}/call/{cid}', [DealController::class, 'callUpdate'])->name('deal.call.update');
    Route::delete('deal/{id}/call/{cid}', [DealController::class, 'callDestroy'])->name('deal.call.destroy');

    Route::get('deal/{id}/email', [DealController::class, 'emailCreate'])->name('deal.email.create');
    Route::post('deal/{id}/email', [DealController::class, 'emailStore'])->name('deal.email.store');

    Route::get('deal/{id}/clients', [DealController::class, 'clientEdit'])->name('deal.clients.edit');
    Route::post('deal/{id}/clients', [DealController::class, 'clientUpdate'])->name('deal.clients.update');
    Route::delete('deal/{id}/clients/{uid}', [DealController::class, 'clientDestroy'])->name('deal.clients.destroy');

    Route::get('deal/{id}/labels', [DealController::class, 'labels'])->name('deal.labels');
    Route::post('deal/{id}/labels', [DealController::class, 'labelStore'])->name('deal.labels.store');


    Route::get('deal/list', [DealController::class, 'deal_list'])->name('deal.list');
    Route::post('deal/change-pipeline', [DealController::class, 'changePipeline'])->name('deal.change.pipeline');


    Route::post('deal/change-deal-status/{id}', [DealController::class, 'changeStatus'])->name('deal.change.status')->middleware(
        [
            'auth',
            'XSS',
            'revalidate',
        ]
    );

    Route::resource('deal', DealController::class);
    Route::post('deal/{id}/note', [DealController::class, 'noteStore'])->name('deal.note.store')->middleware(['auth']);
    // Route::get('category/create', [CategoryController::class, 'create'])->name('category.create');
    // Route::post('category', [CategoryController::class, 'store'])->name('category.store');
    // Route::get('category', [CategoryController::class, 'index'])->name('category.index');
    // Route::get('category/{id}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    // Route::delete('category/{id}/destroy', [CategoryController::class, 'destroy'])->name('category.destroy');
    // Route::put('category/{id}/update', [CategoryController::class, 'update'])->name('category.update');
    Route::resource('operating_hours', OperatinghoursController::class)->middleware('auth','XSS');
    Route::resource('priority', PriorityController::class)->middleware('auth','XSS');
    Route::resource('policiy', SLAPoliciyController::class)->middleware('auth','XSS');
    Route::get('ticket/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('ticket', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('ticket', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('ticket/{id}/edit', [TicketController::class, 'editTicket'])->name('tickets.edit');
    Route::delete('ticket/{id}/destroy', [TicketController::class, 'destroy'])->name('tickets.destroy');
    Route::delete('ticket-attachment/{tid}/destroy/{id}', [TicketController::class, 'attachmentDestroy'])->name('tickets.attachment.destroy');
    Route::put('ticket/{id}/update', [TicketController::class, 'updateTicket'])->name('tickets.update');
    Route::post('ticket/{id}/note', [TicketController::class, 'storeNote'])->name('note.store');


    Route::get('/fileexport', [LeadController::class, 'fileExports'])->name('leads.export');
    Route::get('/import', [LeadController::class, 'fileImportExport'])->name('leads.file.import');
    Route::post('leads/import', [LeadController::class, 'fileImport'])->name('leads.import');
    Route::get('/filesexport', [DealController::class, 'fileExports'])->name('deals.export');
    Route::get('/fileimport', [DealController::class, 'fileImportExport'])->name('deals.file.import');
    Route::post('deals/import', [DealController::class, 'fileImport'])->name('deals.import');

    // ChatGT Settings
    Route::post('chatgptkey', [SettingController::class, 'chatgptkey'])->name('settings.chatgptkey')->middleware(['auth', 'XSS']);
    Route::get('generate/{template_name}', [AiTemplateController::class, 'create'])->name('generate')->middleware(['auth', 'XSS']);
    Route::post('generate/keywords/{id}', [AiTemplateController::class, 'getKeywords'])->name('generate.keywords')->middleware(['auth', 'XSS']);
    Route::post('generate/response', [AiTemplateController::class, 'AiGenerate'])->name('generate.response')->middleware(['auth', 'XSS']);

    // Grammer Check With AI
    Route::get('grammar/{template}', [AiTemplateController::class, 'grammar'])->name('grammar')->middleware(['auth', 'XSS']);
    Route::post('grammar/response', [AiTemplateController::class, 'grammarProcess'])->name('grammar.response')->middleware(['auth', 'XSS']);

    Route::get('user-login/{id}', [UserController::class, 'LoginManage'])->name('users.login');
    Route::any('user-reset-password/{id}', [UserController::class, 'userPassword'])->name('users.reset');
    Route::post('user-reset-password/{id}', [UserController::class, 'userPasswordReset'])->name('user.password.update');
});
Route::get('user/ticket/create', [HomeController::class, 'index'])->name('user.ticket.create');
Route::controller(HomeController::class)->group(function(){

    Route::get('home', 'index')->name('home');
    Route::post('home', 'store')->name('home.store');
    Route::get('user/ticket/search', 'search')->name('user.ticket.search');
    Route::post('search', 'ticketSearch')->name('ticket.search');
    Route::get('tickets/{id}', 'view')->name('home.view');
    Route::post('ticket/{id}', 'reply')->name('user.ticket.reply');
    Route::get('user/faq', 'faq')->name('user.faq');
    Route::get('user/knowledge', 'knowledge')->name('user.knowledge');
    Route::get('knowledgedesc', 'knowledgeDescription')->name('knowledgedesc');

});
Route::resource('bills', BillController::class);
Route::post('ticket/{id}/conversion', [ConversionController::class, 'store'])->name('conversion.store');
Route::get('faqs/create', [FaqController::class, 'create'])->name('faq.create');
Route::post('faq', [FaqController::class, 'store'])->name('faq.store');
Route::get('faq', [FaqController::class, 'index'])->name('faq.index');
Route::get('faq/{id}/edit', [FaqController::class, 'edit'])->name('faq.edit');
Route::delete('faq/{id}/destroy', [FaqController::class, 'destroy'])->name('faq.destroy');
Route::put('faq/{id}/update', [FaqController::class, 'update'])->name('faq.update');
Route::get('knowledge', [KnowledgeController::class, 'index'])->name('knowledge');
Route::get('knowledge/create', [KnowledgeController::class, 'create'])->name('knowledge.create');
Route::post('knowledge', [KnowledgeController::class, 'store'])->name('knowledge.store');
Route::get('knowledge/{id}/edit', [KnowledgeController::class, 'edit'])->name('knowledge.edit');
Route::delete('knowledge/{id}/destroy', [KnowledgeController::class, 'destroy'])->name('knowledge.destroy');
Route::put('knowledge/{id}/update', [KnowledgeController::class, 'update'])->name('knowledge.update');
Route::get('knowledgecategory', [KnowledgebaseCategoryController::class, 'index'])->name('knowledgecategory');
Route::get('knowledgecategory/create', [KnowledgebaseCategoryController::class, 'create'])->name('knowledgecategory.create');
Route::post('knowledgecategory', [KnowledgebaseCategoryController::class, 'store'])->name('knowledgecategory.store');
Route::get('knowledgecategory/{id}/edit', [KnowledgebaseCategoryController::class, 'edit'])->name('knowledgecategory.edit');
Route::delete('knowledgecategory/{id}/destroy', [KnowledgebaseCategoryController::class, 'destroy'])->name('knowledgecategory.destroy');
Route::put('knowledgecategory/{id}/update', [KnowledgebaseCategoryController::class, 'update'])->name('knowledgecategory.update');
Route::any('ticket/custom/field', [TicketCustomFieldController::class, 'index'])->name('ticket.custom.field.index');
Route::any('/custom-fields', [TicketCustomFieldController::class, 'storeCustomFields'])->name('custom-fields.store');
Route::get('export/tickets', [TicketController::class, 'export'])->name('tickets.export');

Route::any('/cookie-consent', [SettingController::class, 'CookieConsent'])->name('cookie-consent');

Route::post('payment-setting', [SettingController::class, 'savePaymentSettings'])->name('payment.settings')->middleware(['auth','verified']);
Route::post('admin-payment-setting', [SettingController::class, 'saveAdminPaymentSettings'])->name('admin.payment.settings')->middleware(['auth','verified']);


Route::get('/bills/pay/{bill_id}', [BillController::class, 'payinvoice'])->name('pay.invoice')->middleware(['XSS']);

Route::post('bills/{id}/payment', [StripePaymentController::class, 'addpayment'])->name('invoice.payment')->middleware(['XSS']);

Route::post('bills/{id}/bill-with-paypal', [PaypalController::class,'PayWithPaypal'])->name('bill.with.paypal')->middleware(['XSS']);
Route::get('{id}/get-payment-status/{amount}', [PaypalController::class,'GetPaymentStatus'])->name('get.payment.status')->middleware(['XSS']);

Route::POST('bills/getclientdetail', [BillController::class,'getClientDetail'])->name('get.client.detail');
Route::POST('bills/getadvocatedetail', [BillController::class,'getadvocateDetail'])->name('get.advocate.detail');

Route::post('/invoice-pay-with-paystack', [PaystackPaymentController::class, 'invoicePayWithPaystack'])->name('invoice.pay.with.paystack')->middleware(['XSS']);
Route::get('/invoice/paystack/{invoice_id}/{amount}/{pay_id}', [PaystackPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.paystack')->middleware(['XSS']);

Route::post('/invoice-pay-with-flaterwave', [FlutterwavePaymentController::class, 'invoicePayWithFlutterwave'])->name('invoice.pay.with.flaterwave')->middleware(['XSS']);
Route::get('/invoice/flaterwave/{txref}/{invoice_id}', [FlutterwavePaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.flaterwave')->middleware(['XSS']);

Route::post('/invoice-pay-with-razorpay', [RazorpayPaymentController::class, 'invoicePayWithRazorpay'])->name('invoice.pay.with.razorpay')->middleware(['XSS']);
Route::get('/invoice/razorpay/{txref}/{invoice_id}', [RazorpayPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.razorpay');

Route::post('/invoice-pay-with-mercado', [MercadoPaymentController::class, 'invoicePayWithMercado'])->middleware(['XSS'])->name('invoice.pay.with.mercado');
Route::any('/invoice/mercado/{invoice}', [MercadoPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.mercado')->middleware(['XSS']);

Route::post('/invoice-pay-with-paytm', [PaytmPaymentController::class, 'invoicePayWithPaytm'])->middleware(['XSS'])->name('invoice.pay.with.paytm');
Route::post('/invoice/paytm/{invoice}', [PaytmPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.paytm')->middleware(['XSS']);

Route::post('/invoice-pay-with-mollie', [MolliePaymentController::class, 'invoicePayWithMollie'])->middleware(['XSS'])->name('invoice.pay.with.mollie');
Route::get('/invoice/mollie/{invoice}', [MolliePaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.mollie')->middleware(['XSS']);

Route::post('/invoice-pay-with-skrill', [SkrillPaymentController::class, 'invoicePayWithSkrill'])->middleware(['XSS'])->name('invoice.pay.with.skrill');
Route::get('/invoice/skrill/{invoice}', [SkrillPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.skrill')->middleware(['XSS']);

Route::post('/invoice-pay-with-coingate', [CoingatePaymentController::class, 'invoicePayWithCoingate'])->middleware(['XSS'])->name('invoice.pay.with.coingate');
Route::get('/invoice/coingate/{invoice}', [CoingatePaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.coingate')->middleware(['XSS']);

Route::post('/invoicepayment', [PaymentWallController::class, 'invoicePayWithPaymentwall'])->name('paymentwall.invoice');
Route::post('/invoice-pay-with-paymentwall/{invoice}', [PaymentWallController::class, 'getInvoicePaymentStatus'])->name('invoice-pay-with-paymentwall');
Route::any('/invoice/error/{flag}/{invoice_id}', [PaymentWallController::class, 'invoiceerror'])->name('error.invoice.show');

Route::post('/invoice-with-toyyibpay', [ToyyibpayController::class, 'invoicepaywithtoyyibpay'])->name('invoice.with.toyyibpay');
Route::get('/invoice-toyyibpay-status/{amount}/{invoice_id}', [ToyyibpayController::class, 'invoicetoyyibpaystatus'])->name('invoice.toyyibpay.status');

Route::post('/invoice-with-payfast', [PayfastController::class, 'invoicepaywithpayfast'])->name('invoice.with.payfast');
Route::get('/invoice-payfast-status/{invoice_id}', [PayfastController::class, 'invoicepayfaststatus'])->name('invoice.payfast.status');

Route::any('/pay-with-bank', [BankTransferController::class, 'invoicePayWithbank'])->name('invoice.pay.with.bank');
Route::get('bankpayment/show/{id}', [BankTransferController::class, 'bankpaymentshow'])->name('bankpayment.show');
Route::delete('invoice/bankpayment/{id}/delete', [BankTransferController::class, 'invoicebankPaymentDestroy'])->name('invoice.bankpayment.delete');
Route::post('/invoice/status/{id}', [BankTransferController::class, 'invoicebankstatus'])->name('invoice.status');

Route::post('/invoice-with-iyzipay', [IyziPayController::class, 'invoicepaywithiyzipay'])->name('invoice.with.iyzipay');
Route::post('/invoice-iyzipay-status/{invoice_id}/{amount}', [IyziPayController::class, 'invoiceiyzipaystatus'])->name('invoice.iyzipay.status');

Route::post('/customer-pay-with-sspay', [SspayController::class,'invoicepaywithsspaypay'])->name('customer.pay.with.sspay');
Route::get('/customer/sspay/{invoice}/{amount}', [SspayController::class,'getInvoicePaymentStatus'])->name('customer.sspay');

Route::post('invoice-with-paytab/', [PaytabController::class, 'invoicePayWithpaytab'])->name('pay.with.paytab');
Route::any('invoice-paytab-status/{invoice}/{amount}', [PaytabController::class, 'PaytabGetPaymentCallback'])->name('invoice.paytab.status');

Route::post('invoice-with-benefit/', [BenefitPaymentController::class, 'invoicePayWithbenefit'])->name('pay.with.paytab');
Route::any('invoice-benefit-status/{invoice_id}/{amount}', [BenefitPaymentController::class, 'getInvociePaymentStatus'])->name('invoice.benefit.status');


// cashfree
Route::post('invoice-with-cashfree/', [CashfreeController::class, 'invoicePayWithcashfree'])->name('pay.with.cashfree');
Route::any('invoice-cashfree-status/', [CashfreeController::class, 'getInvociePaymentStatus'])->name('invoice.cashfree.status');


Route::post('invoice-with-aamarpay/', [AamarpayController::class, 'invoicePayWithaamarpay'])->name('pay.with.aamarpay');
Route::any('invoice-aamarpay-status/{data}', [AamarpayController::class, 'getInvociePaymentStatus'])->name('invoice.aamarpay.status');


Route::post('invoice-with-paytr/', [PaytrController::class, 'invoicePayWithpaytr'])->name('invoice.with.paytr');
Route::any('invoice-paytr-status/', [PaytrController::class, 'getInvociePaymentStatus'])->name('invoice.paytr.status');

Route::post('invoice-with-yookassa/', [YooKassaController::class, 'invoicePayWithYookassa'])->name('invoice.with.yookassa');
Route::any('invoice-yookassa-status/', [YooKassaController::class, 'getInvociePaymentStatus'])->name('invoice.yookassa.status');

Route::any('invoice-with-midtrans/', [MidtransController::class, 'invoicePayWithMidtrans'])->name('invoice.with.midtrans');
Route::any('invoice-midtrans-status/', [MidtransController::class, 'getInvociePaymentStatus'])->name('invoice.midtrans.status');

Route::any('/invoice-with-xendit', [XenditPaymentController::class, 'invoicePayWithXendit'])->name('invoice.with.xendit');
Route::any('/invoice-xendit-status', [XenditPaymentController::class, 'getInvociePaymentStatus'])->name('invoice.xendit.status');

Route::post('invoice-payhere-payment', [PayHereController::class, 'invoicePayWithPayHere'])->name('invoice.payhere.payment');
Route::get('/invoice-payhere-status', [PayHereController::class, 'invoiceGetPayHereStatus'])->name('invoice.payhere.status');

Route::get('/verify-otp', [AuthenticatedSessionController::class, 'showOtpForm'])->name('verify.otp');
Route::post('/verify-otp', [AuthenticatedSessionController::class, 'verifyOtp']);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ],
    function () {

        Route::resource('holiday', HolidayController::class);
    }
);

Route::group(
    ['middleware' => ['auth', 'XSS', 'revalidate',],],
    function () {
        Route::get('leave/{id}/action', [LeaveController::class, 'action'])->name('leave.action');
        Route::post('leave/changeAction', [LeaveController::class, 'changeAction'])->name('leave.changeaction');
        Route::post('leave/jsonCount', [LeaveController::class, 'jsonCount'])->name('leave.jsoncount');
        Route::get('leave/calendar', [LeaveController::class, 'calendar'])->name('leave.calendar');
        Route::resource('leave', LeaveController::class);

    }
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ],
    function () {
        Route::resource('leaveType', LeaveTypeController::class);
    }
);

Route::group(
    ['middleware' => ['auth', 'XSS', 'revalidate',],],
    function () {
        Route::get('meeting/calendar', [MeetingController::class, 'calendar'])->name('meeting.calendar');
        Route::resource('meeting', MeetingController::class);
    }
);


Route::resource('company-policy', CompanyPolicyController::class)->middleware(
    [
        'auth',
        'XSS',
        'revalidate',
    ]
);


Route::resource('award', AwardController::class)->middleware(
    [
        'auth',
        'XSS',
        'revalidate',
    ]
);

Route::resource('award-type', AwardTypeController::class)->middleware(
    [
        'auth',
        'XSS',
        'revalidate',
    ]
);

Route::resource('practice-area', PracticeAreaController::class)->middleware(
    [
        'auth',
        'XSS',
        'revalidate',
    ]
);

Route::resource('account-assets', AssetController::class)->middleware(
    [
        'auth',
        'XSS',
        'revalidate',
    ]
);
Route::resource('document-upload', DocumentUploadController::class)->middleware(
    [
        'auth',
        'XSS',
        'revalidate',
    ]
);

Route::resource('company-policy', CompanyPolicyController::class)->middleware(
    [
        'auth',
        'XSS',
        'revalidate',
    ]
);


Route::resource('award', AwardController::class)->middleware(
    [
        'auth',
        'XSS',
        'revalidate',
    ]
);

Route::resource('transfer', TransferController::class)->middleware(
    [
        'auth',
        'XSS',
        'revalidate',
    ]
);

Route::resource('award-type', AwardTypeController::class)->middleware(
    [
        'auth',
        'XSS',
        'revalidate',
    ]
);

Route::resource('resignation', ResignationController::class)->middleware(
    [
        'auth',
        'XSS',
        'revalidate',
    ]
);

Route::resource('trip', TripController::class)->middleware(
    [
        'auth',
        'XSS',
        'revalidate',
    ]
);

Route::resource('promotion', PromotionController::class)->middleware(
    [
        'auth',
        'XSS',
        'revalidate',
    ]
);
Route::resource('complaint', ComplaintController::class)->middleware(
    [
        'auth',
        'XSS',
        'revalidate',
    ]
);

Route::resource('warning', WarningController::class)->middleware(
    [
        'auth',
        'XSS',
        'revalidate',
    ]
);


Route::resource('termination', TerminationController::class)->middleware(
    [
        'auth',
        'XSS',
        'revalidate',
    ]
);
Route::resource('termination-type', TerminationTypeController::class)->middleware(
    [
        'auth',
        'XSS',
        'revalidate',
    ]
);

Route::resource('indicator', IndicatorController::class)->middleware(
    [
        'auth',
        'XSS',
        'revalidate',
    ]
);


Route::resource('appraisal', AppraisalController::class)->middleware(
    [
        'auth',
        'XSS',
        'revalidate',
    ]
);
Route::resource('training-type', TrainingTypeController::class)->middleware(
    [
        'auth',
        'XSS',
        'revalidate',
    ]
);
Route::resource('performanceType', PerformanceTypeController::class)->middleware(
    [
        'auth',
        'XSS',
        'revalidate',
    ]
);
Route::resource('competencies', CompetenciesController::class)->middleware(
    [
        'auth',
        'XSS',
        'revalidate',
    ]
);
Route::resource('trainer', TrainerController::class)->middleware(
    [
        'auth',
        'XSS',
        'revalidate',
    ]
);


Route::post('training/status', [TrainingController::class, 'updateStatus'])->name('training.status')->middleware(['auth', 'XSS', 'revalidate',]);

Route::resource('training', TrainingController::class)->middleware(
    [
        'auth',
        'XSS',
        'revalidate',
    ]
);
Route::resource('goaltracking', GoalTrackingController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);



Route::group(['middleware' => ['verified']], function () {
    Route::get('import/assets/file', [AssetController::class, 'importFile'])->name('asset.file.import')->middleware(
        [
            'auth',
            'XSS',
        ]
    );

    Route::post('import/assets', [AssetController::class, 'import'])->name('assets.import')->middleware(
        [
            'auth',
            'XSS',
        ]
    );
    Route::group(
        ['middleware' => ['auth', 'XSS', 'revalidate',],],
        function () {
            Route::post('edit-employee-company-info/{id}', [EmployeeController::class, 'employeeCompanyInfoEdit'])->name('employee.company.update');
            Route::post('edit-employee-personal-info/{id}', [EmployeeController::class, 'employeePersonalInfoEdit'])->name('employee.personal.update');
            Route::post('edit-employee-bank-info/{id}', [EmployeeController::class, 'employeeBankInfoEdit'])->name('employee.bank.update');

            Route::resource('employee', EmployeeController::class);
            Route::any('employee-reset-password/{id}', [EmployeeController::class, 'employeePassword'])->name('employee.reset');
            Route::post('employee-reset-password/{id}', [EmployeeController::class, 'employeePasswordReset'])->name('employee.password.update');
            Route::get('employee-login/{id}', [EmployeeController::class, 'LoginManage'])->name('employee.login');
        }
    );

    Route::post('employee/getdepartment', [EmployeeController::class, 'getDepartment'])->name('employee.getdepartment')->middleware(['auth', 'XSS']);
    Route::post('employee/json', [EmployeeController::class, 'json'])->name('employee.json')->middleware(['auth', 'XSS', 'revalidate',]);

    Route::group(['middleware' => ['auth', 'XSS', 'revalidate',],], function () {
        Route::resource('client', ClientController::class);
    });
    Route::any('client-reset-password/{id}', [ClientController::class, 'clientPassword'])->name('client.reset');
    Route::post('client-reset-password/{id}', [ClientController::class, 'clientPasswordReset'])->name('client.password.update');
    Route::get('client-login/{id}', [ClientController::class, 'LoginManage'])->name('client.login');

    Route::any('event/get_event_data', [EventController::class, 'get_event_data'])->name('event.get_event_data')->middleware(['auth', 'XSS']);
    Route::any('holiday/get_holiday_data', [HolidayController::class, 'get_holiday_data'])->name('holiday.get_holiday_data')->middleware(['auth', 'XSS']);
    Route::any('meeting/get_holiday_data', [MeetingController::class, 'get_holiday_data'])->name('meeting.get_holiday_data')->middleware(['auth', 'XSS']);
    Route::any('zoom-meeting/get_holiday_data', [ZoommeetingController::class, 'get_holiday_data'])->name('zoom-meeting.get_holiday_data')->middleware(['auth', 'XSS']);
    Route::any('leave/get_holiday_data', [LeaveController::class, 'get_holiday_data'])->name('leave.get_holiday_data')->middleware(['auth', 'XSS']);
    Route::any('task/get_holiday_data', [ProjectController::class, 'get_holiday_data'])->name('task.get_holiday_data')->middleware(['auth', 'XSS']);

    Route::get('export/meeting', [MeetingController::class, 'export'])->name('meeting.export');

    Route::get('export/award', [AwardController::class, 'export'])->name('award.export');

    Route::get('export/invoice', [InvoiceController::class, 'export'])->name('invoice.export');

    Route::get('export/creditnote', [CreditNoteController::class, 'export'])->name('creditnote.export');

    Route::get('export/goal', [GoalController::class, 'export'])->name('goal.export');

    Route::get('import/attendance/file', [AttendanceController::class, 'importFile'])->name('attendance.file.import')->middleware(
        [
            'auth',
            'XSS',
        ]
    );
    
    Route::any('import/attendance', [AttendanceController::class, 'import'])->name('attendance.import')->middleware(
        [
            'auth',
            'XSS',
        ]
    );
    
    Route::get('import/holiday/file', [HolidayController::class, 'importFile'])->name('holiday.file.import')->middleware(
        [
            'auth',
            'XSS',
        ]
    );
    
    Route::post('import/holiday', [HolidayController::class, 'import'])->name('holiday.import')->middleware(
        [
            'auth',
            'XSS',
        ]
    );
    Route::group(
        [
            'middleware' => [
                'auth',
                'XSS',
                'revalidate',
            ],
        ],
        function () {
            Route::get('bulk-attendance', [AttendanceController::class, 'bulkAttendance'])->name('bulk.attendance');
            Route::post('bulk-attendance', [AttendanceController::class, 'bulkAttendanceData'])->name('bulk.attendance');
            Route::post('employee/attendance', [AttendanceController::class, 'attendance'])->name('employee.attendance');
            Route::resource('attendance', AttendanceController::class);
        }
    );

    Route::group(
        [
            'middleware' => [
                'auth',
                'XSS',
                'revalidate',
            ],
        ],
        function () {
            Route::resource('department', DepartmentController::class);
        }
    );
    Route::group(
        [
            'middleware' => [
                'auth',
                'XSS',
                'revalidate',
            ],
        ],
        function () {
            Route::resource('designation', DesignationController::class);
        }
    );
    Route::group(
        [
            'middleware' => [
                'auth',
                'XSS',
                'revalidate',
            ],
        ],
        function () {
            Route::resource('salaryType', SalaryTypeController::class);
        }
    );
    Route::resource('branch', BranchController::class)->middleware(['auth', 'XSS',]);

    Route::resource('goaltype', GoalTypeController::class)->middleware(
        ['auth', 'XSS',]
    );
    Route::group(
        [
            'middleware' => [
                'auth',
                'XSS',
                'revalidate',
            ],
        ],
        function () {
            Route::resource('category', CategoryController::class);
        }
    );
});
//-=======appricalStar==========
Route::post('/appraisals', [AppraisalController::class, 'empByStar'])->name('empByStar')->middleware(['auth', 'XSS']);
Route::post('/appraisals1', [AppraisalController::class, 'empByStar1'])->name('empByStar1')->middleware(['auth', 'XSS']);
Route::post('/getemployee', [AppraisalController::class, 'getemployee'])->name('getemployee');
