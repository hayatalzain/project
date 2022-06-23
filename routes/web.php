<?php

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

Auth::routes();


//admin password reset routes
// Route::get('admin/password/reset','Auth\AdminForgotPasswordController@showLinkRequestForm')->name('password.request');
// Route::post('admin/password/email','Auth\AdminForgotPasswordController@sendResetLinkEmail')->name('password.email');
// Route::post('admin/password/reset','Auth\AdminResetPasswordController@reset');
// Route::get('admin/password/reset/{token}','Auth\AdminResetPasswordController@showResetForm')->name('password.reset');


//Route::post('password/email', 'Auth\ForgotPasswordController@getResetToken');
//Route::post('password/reset', 'Auth\ResetPasswordController@reset');


//Route::resource('scrape','Admin\WebScraperController');


if (config('app.debug')) {
    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
}



/*New rout Favorite Devices*/

Route::get('favorite/devices', 'FavoriteDevicesController@IndexFavoriteDevices')->name('favorite.devices.index');

//Route::get('/admin/login', 'Auth\AdminLoginController@showLoginForm')->name('login');
//Route::post('/admin/login', 'Auth\AdminLoginController@login')->name('login');

//Route::get('/login', 'Auth\AdminLoginController@showLoginForm')->name('login');
//Route::get('/register', 'HomeController@index')->name('register');


Route::group(['middleware'=>'auth'], function(){

    Route::get('/dataTable', 'Controller@dataTableData')->name('dataTable');

    Route::get('/', 'HomeController@index')->name('admin');
    Route::get('/me', 'HomeController@MyAccount')->name('me');
    Route::put('/me', 'HomeController@UpdateMyAccount')->name('me');


    Route::middleware(['permission:manage_admins'])->group(function(){
        Route::resource('admins', 'AdminsController',['names'=>
            [
                'index' => 'admins',
                'create' => 'admins.create',
                'store' => 'admins.store',
                'edit' => 'admins.edit',
                'update' => 'admins.update',
                'destroy' => 'admins.destroy',
            ], 'except' => ['show']
     ]);
    });


    Route::get('/users/export/{role?}','UsersController@export')->name('users.export');

    Route::middleware(['permission:manage_clients'])->group(function(){
        Route::get('/clients/','UsersController@clients')->name('clients');
        Route::get('/client/list/export','UsersController@ExportAllClients')->name('client.all.export');

        Route::get('/technicals/','UsersController@technicals')->name('technicals');
        Route::get('/technicalsTest/','UsersController@technicalsTest')->name('technicalsTest');
        Route::resource('users', 'UsersController',['names'=>
            [
                'create' => 'users.create',
                'store' => 'users.store',
                'edit' => 'users.edit',
                'update' => 'users.update',
                'destroy' => 'users.destroy',
            ],'except' => ['index']
        ]);

        Route::get('/favorite/{id}/devices/','UsersController@FavoriteDevices')->name('favorite_devices');
        Route::post('/favorite/devices/create','UsersController@CraeteFavoriteDevices')->name('create_favorite_devices');
        Route::get('/favorite/{id}/devices/{issue}','UsersController@EditFavoriteDevices')->name('edit_favorite_devices')->where('issue','[0-9]+');;
        Route::put('/favorite/{id}/devices/{issue}','UsersController@UpdateFavoriteDevices')->name('update_favorite_devices');
        Route::post('/favorite/{id}/devices/','UsersController@StoreFavoriteDevices')->name('store_favorite_devices');
        Route::get('/favorite/{id}/','UsersController@AllFavoriteDevices1')->name('all_favorite_devices');
        Route::delete('/favorite/{id}/devices/{issue}','UsersController@DestroyFavoriteDevices')->name('destroy_favorite_devices');
        Route::delete('/favorite/devices/{id}/all','UsersController@DestroyFavoriteDevicesAll')->name('destroy_favorite_devices_all');
    });

    Route::get('/tech/list/export','UsersController@ExportAllTechnicals')->name('tech.all.export');

    Route::get('/technicals/list','ReportsController@TechnicalsReport')->name('tech.list');
    Route::get('/technicals/list/export','ReportsController@ExportTechnicalsReport')->name('tech.list.export');
    //  Route::get('/technicals/financial_report','ReportsController@tech_financial_report')->name('tech.financial_report');


    Route::get('/technicals_most_orders/list','ReportsController@TechnicalsMostHaveOrdersReport')->name('technicals_most_orders.list');
    Route::get('/technicals_most_orders/list/export','ReportsController@ExportTechnicalsMostHaveOrdersReport')->name('technicals_most_orders.list.export');

    Route::resource('contacts', 'ContactsController',['names'=>
        [
            'index' => 'contacts',
            'show' => 'contacts.show',
            'destroy' => 'contacts.destroy',
        ]
    ]);

    Route::middleware(['permission:mangae_devices'])->group(function(){

        Route::get('devices/{id}/issues-price', 'DevicesController@issuesPrice')->name('devices.issues-price');
        Route::get('devices/{id}/issues-price/{issue}', 'DevicesController@editIssuePrice')->name('devices.issues-price-edit')->where('issue','[0-9]+');
        Route::put('devices/{id}/issues-price/{issue}', 'DevicesController@updateIssuePrice')->name('devices.issues-price-update');
        Route::get('devices/{id}/issues-price/create', 'DevicesController@createIssuePrice')->name('devices.issues-price-create');
        Route::post('devices/{id}/issues-price/', 'DevicesController@storeIssuePrice')->name('devices.issues-price-store');
        Route::delete('devices/{id}/issues-price/{issue}', 'DevicesController@DestroyIssuesPrice')->name('devices.issues-price-destroy');
        /*new devices*/

        Route::resource('devices', 'DevicesController',['names'=>
            [
                'index'  => 'devices',
                'create' => 'devices.create',
                'store'  => 'devices.store',
                'edit'   => 'devices.edit',
                'update' => 'devices.update',
                'destroy' => 'devices.destroy',
                'show' => 'devices.show',
            ]
        ]);
        Route::get('/devices/list/export','DevicesController@ExportAllDevices')->name('devices.list.export');

        /*new brand*/
        Route::resource('brand', 'BrandController',['names'=>
            [
                'index'   => 'brand',
                'create'  => 'brand.create',
                'store'   => 'brand.store',
                'show' => 'brand.show',
                'edit'    => 'brand.edit',
                'update'  => 'brand.update',
                'destroy' => 'brand.destroy',
            ]
        ]);

    });



    Route::middleware(['permission:manage_issues'])->group(function(){

        Route::get('/issues/list/export','IssuesController@ExportAllIssues')->name('issue.list.export');

        Route::resource('issues', 'IssuesController',['names'=>
            [
                'index'  => 'issues',
                'create' => 'issues.create',
                'store'  => 'issues.store',
                'edit'   => 'issues.edit',
                'update' => 'issues.update',
                'destroy' => 'issues.destroy',
            ]
        ]);
    });


    Route::middleware(['permission:manage_colors'])->group(function(){

        Route::resource('colors', 'ColorsController',['names'=>
            [
                'index'     => 'colors',
                'create'    => 'colors.create',
                'store'     => 'colors.store',
                'edit'      => 'colors.edit',
                'update'    => 'colors.update',
                'destroy'   => 'colors.destroy',
            ]
        ]);

    });


    Route::middleware(['permission:manage_offers'])->group(function(){
        Route::resource('offers', 'OffersController',['names'=>
            [
                'index'  => 'offers',
                'create' => 'offers.create',
                'store'  => 'offers.store',
                'edit'   => 'offers.edit',
                'update' => 'offers.update',
                'destroy' => 'offers.destroy',
            ]
        ]);
//    Route::get('/offers/list','OffersController@OffersReport')->name('offer.list');
    Route::get('/offers/list/export','OffersController@ExportOffersReport')->name('offer.list.export');
    });


    Route::middleware(['permission:manage_regions'])->group(function(){
        Route::resource('regions', 'RegionsController',['names'=>
            [
                'index'  => 'regions',
                'create' => 'regions.create',
                'store'  => 'regions.store',
                'edit'   => 'regions.edit',
                'update' => 'regions.update',
                'destroy' => 'regions.destroy',
            ]
        ]);


        Route::resource('regions_details', 'RegionsDetailsController',['names'=>
            [
                'index'  => 'regions_details',
                'create' => 'regions_details.create',
                'store'  => 'regions_details.store',
                'edit'   => 'regions_details.edit',
                'update' => 'regions_details.update',
                'destroy' => 'regions_details.destroy',
            ]
        ]);

    });


    Route::middleware(['permission:manage_reject_reason'])->group(function(){
        Route::resource('reject_reason', 'RejectReasonsController',['names'=>
            [
                'index'   => 'reject_reason',
                'create'  => 'reject_reason.create',
                'store'   => 'reject_reason.store',
                'edit'    => 'reject_reason.edit',
                'update'  => 'reject_reason.update',
                'destroy' => 'reject_reason.destroy',
            ]
        ]);
    });

    Route::middleware(['permission:manage_notifications'])->group(function(){

        Route::get('notifications/{notification}/publish', 'NotificationsController@publish')->name('notifications.publish');

        Route::resource('notifications', 'NotificationsController',['names'=>
            [
                'index'   => 'notifications',
                'create'  => 'notifications.create',
                'store'   => 'notifications.store',
                'edit'    => 'notifications.edit',
                'update'  => 'notifications.update',
                'destroy' => 'notifications.destroy',
            ],'except'=>['show']
        ]);
    });

    Route::middleware(['permission:manage_faq'])->group(function(){
        Route::resource('faq', 'FaqController',['names'=>
            [
                'index'  => 'faq',
                'create' => 'faq.create',
                'store'  => 'faq.store',
                'edit'   => 'faq.edit',
                'update' => 'faq.update',
                'destroy' => 'faq.destroy',
            ]
        ]);
/*new*/
        Route::resource('terms', 'TermsController',['names'=>
            [
                'index'   => 'terms',
                'create'  => 'terms.create',
                'store'   => 'terms.store',
                'show' => 'terms.show',
                'edit'    => 'terms.edit',
                'update'  => 'terms.update',
                'destroy' => 'terms.destroy',
            ]
        ]);
    });
    Route::get('orders/{id}/confirm-payment', 'OrdersController@confirmPayment')->name('orders.confirm-payment');

    Route::middleware(['permission:show_orders'])->group(function(){

        Route::get('orders/{id}/history', 'OrdersController@History')->name('orders.history');

        Route::resource('orders', 'OrdersController',['names'=>
            [
                'index'  => 'orders',
                'show'   => 'orders.show',
                'edit'   => 'orders.edit',
                'update' => 'orders.update',
                'destroy' => 'orders.destroy',
            ],
            'except' => ['edit','create','store']
        ]);

        Route::get('/orders/list/export','OrdersController@ExportAllOrders')->name('order.list.export');

    });


    Route::middleware(['permission:show_report_dashboard'])->group(function(){

        Route::get('/reports/total-orders-in-date', 'ReportsController@getNumberOrdersByDate')->name('reports.getNumberOrdersByDate');

        Route::get('/reports/most-devices-repaired', 'ReportsController@getMostRepairedDevices')->name('reports.getMostRepairedDevices');
    });


    Route::middleware(['permission:show_financial_report'])->group(function(){

        Route::get('/reports/financial/export','ReportsController@ExportReportsFinancial')->name('reports.financial.export');
        Route::get('/reports/financial', 'ReportsController@getFinancial')->name('reports.financial');
        Route::get('/reports/GetTotal-financial', 'ReportsController@getTotalFinancial')->name('reports.getTotalFinancial');
    });


    Route::get('lang/{local}', function($local){
        session(['lang'=>$local]);
        return back();
    })->name('switch-language');


    Route::middleware(['permission:manage_settings'])->group(function(){

        Route::resource('settings', 'SettingsController',['names'=>
            [
                'index' => 'settings',
                //  'update' => 'settings.update',
            ],'only' => ['index']
        ]);

        Route::post('/settings', 'SettingsController@save')->name('settings.save');
/*new*/
        Route::resource('appointments', 'AppointmentsController',['names'=>
            [
                'index'   => 'appointments',
                'create'  => 'appointments.create',
                'store'   => 'appointments.store',
                'show' => 'appointments.show',
                'edit'    => 'appointments.edit',
                'update'  => 'appointments.update',
                'destroy' => 'appointments.destroy',
            ]
        ]);

    });

    // Route::post('/admin/logout', 'Auth\AdminLoginController@logout')->name('logout');

});  // Auth Group





