<?php

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', 'App\Http\Controllers\Auth\UserAuthController@register')->middleware('throttle:auth-api');
    Route::post('login', 'App\Http\Controllers\Auth\UserAuthController@login')->middleware('throttle:login');
    Route::post('forgot-password', 'App\Http\Controllers\Auth\UserAuthController@forgotPassword')->middleware('throttle:auth-api');
    Route::post('reset-password', 'App\Http\Controllers\Auth\UserAuthController@resetPassword')->middleware('throttle:auth-api');
});

Route::middleware('api.auth')->group(function () {
    Route::get('auth/me', 'App\Http\Controllers\Auth\UserAuthController@me');
    Route::post('auth/logout', 'App\Http\Controllers\Auth\UserAuthController@logout');
    Route::put('auth/profile', 'App\Http\Controllers\Auth\UserAuthController@updateProfile');
    Route::put('auth/change-password', 'App\Http\Controllers\Auth\UserAuthController@changePassword');

    Route::get('referral', 'App\Http\Controllers\Front\ReferralController@getReferralCode');
    Route::get('financial', 'App\Http\Controllers\Front\FinancialController@index');
    Route::get('financial/{id}', 'App\Http\Controllers\Front\FinancialController@show');
    Route::post('financial/deposit', 'App\Http\Controllers\Front\FinancialController@deposit');
    Route::post('financial/withdraw', 'App\Http\Controllers\Front\FinancialController@withdraw');

    Route::get('user/balance', 'App\Http\Controllers\User\UserController@balance');
    Route::get('user/articles', 'App\Http\Controllers\User\ArticleController@index');
    Route::get('user/articles/{id}', 'App\Http\Controllers\User\ArticleController@show');
    Route::post('user/articles', 'App\Http\Controllers\User\ArticleController@store');
    Route::put('user/articles/{id}', 'App\Http\Controllers\User\ArticleController@update');
    Route::delete('user/articles/{id}', 'App\Http\Controllers\User\ArticleController@destroy');
    Route::get('user/notifications', 'App\Http\Controllers\User\NotificationController@index');
    Route::get('user/notifications/{id}', 'App\Http\Controllers\User\NotificationController@show');
    Route::post('user/notifications/{id}/read', 'App\Http\Controllers\User\NotificationController@markRead');
    Route::post('user/notifications/read-all', 'App\Http\Controllers\User\NotificationController@markAllRead');
    Route::delete('user/notifications/{id}', 'App\Http\Controllers\User\NotificationController@destroy');
});

Route::prefix('front')->group(function () {
    Route::get('articles/categories', 'App\Http\Controllers\Front\ArticleController@categories');
    Route::get('articles', 'App\Http\Controllers\Front\ArticleController@index');
    Route::get('articles/{id}', 'App\Http\Controllers\Front\ArticleController@show');
    Route::get('products/categories', 'App\Http\Controllers\Front\ProductController@categories');
    Route::get('products', 'App\Http\Controllers\Front\ProductController@index');
    Route::get('products/{id}', 'App\Http\Controllers\Front\ProductController@show');
    Route::get('referral/info/{code}', 'App\Http\Controllers\Front\ReferralController@getReferralInfo');
    Route::get('settings', 'App\Http\Controllers\Front\SettingController@index');
    Route::get('settings/{key}', 'App\Http\Controllers\Front\SettingController@show');
    Route::get('sitemap.xml', 'App\Http\Controllers\Front\SitemapController@index');
});

Route::prefix('admin')->group(function () {
    Route::post('login', 'App\Http\Controllers\Auth\AdminAuthController@login')->middleware('throttle:login');
    Route::post('forgot-password', 'App\Http\Controllers\Auth\AdminAuthController@forgotPassword')->middleware('throttle:auth-api');
    Route::post('reset-password', 'App\Http\Controllers\Auth\AdminAuthController@resetPassword')->middleware('throttle:auth-api');
});

Route::middleware('admin.auth')->prefix('admin')->group(function () {
    Route::get('me', 'App\Http\Controllers\Auth\AdminAuthController@me');
    Route::post('logout', 'App\Http\Controllers\Auth\AdminAuthController@logout');
    Route::put('profile', 'App\Http\Controllers\Auth\AdminAuthController@updateProfile');
    Route::put('change-password', 'App\Http\Controllers\Auth\AdminAuthController@changePassword');

    Route::get('dashboard', 'App\Http\Controllers\Admin\DashboardController@index');

    Route::get('users', 'App\Http\Controllers\Admin\UserController@index');
    Route::post('users', 'App\Http\Controllers\Admin\UserController@store');
    Route::get('users/{id}', 'App\Http\Controllers\Admin\UserController@show');
    Route::put('users/{id}', 'App\Http\Controllers\Admin\UserController@update');
    Route::delete('users/batch', 'App\Http\Controllers\Admin\UserController@batchDestroy');
    Route::delete('users/{id}', 'App\Http\Controllers\Admin\UserController@destroy');

    Route::get('admins', 'App\Http\Controllers\Admin\AdminController@index');
    Route::get('admins/{id}', 'App\Http\Controllers\Admin\AdminController@show');
    Route::post('admins', 'App\Http\Controllers\Admin\AdminController@store');
    Route::put('admins/{id}', 'App\Http\Controllers\Admin\AdminController@update');
    Route::delete('admins/{id}', 'App\Http\Controllers\Admin\AdminController@destroy');

    Route::get('roles', 'App\Http\Controllers\Admin\RoleController@index');
    Route::get('roles/{id}', 'App\Http\Controllers\Admin\RoleController@show');
    Route::post('roles', 'App\Http\Controllers\Admin\RoleController@store');
    Route::put('roles/{id}', 'App\Http\Controllers\Admin\RoleController@update');
    Route::delete('roles/{id}', 'App\Http\Controllers\Admin\RoleController@destroy');
    Route::get('permissions', 'App\Http\Controllers\Admin\RoleController@permissions');

    Route::get('articles', 'App\Http\Controllers\Admin\ArticleController@index');
    Route::get('articles/{id}', 'App\Http\Controllers\Admin\ArticleController@show');
    Route::post('articles', 'App\Http\Controllers\Admin\ArticleController@store');
    Route::put('articles/{id}', 'App\Http\Controllers\Admin\ArticleController@update');
    Route::delete('articles/{id}', 'App\Http\Controllers\Admin\ArticleController@destroy');

    Route::get('menus', 'App\Http\Controllers\Admin\MenuController@index');
    Route::get('menus/all', 'App\Http\Controllers\Admin\MenuController@all');
    Route::get('menus/{id}', 'App\Http\Controllers\Admin\MenuController@show');
    Route::post('menus', 'App\Http\Controllers\Admin\MenuController@store');
    Route::put('menus/{id}', 'App\Http\Controllers\Admin\MenuController@update');
    Route::delete('menus/{id}', 'App\Http\Controllers\Admin\MenuController@destroy');

    Route::get('financial/statistics', 'App\Http\Controllers\Admin\FinancialController@statistics');
    Route::get('financial', 'App\Http\Controllers\Admin\FinancialController@index');
    Route::get('financial/{id}', 'App\Http\Controllers\Admin\FinancialController@show');
    Route::post('financial', 'App\Http\Controllers\Admin\FinancialController@store');

    Route::get('notifications', 'App\Http\Controllers\Admin\NotificationController@index');
    Route::post('notifications', 'App\Http\Controllers\Admin\NotificationController@store');
    Route::delete('notifications/{id}', 'App\Http\Controllers\Admin\NotificationController@destroy');
    Route::delete('notifications/batch', 'App\Http\Controllers\Admin\NotificationController@batchDelete');

    Route::get('operation-logs', 'App\Http\Controllers\Admin\OperationLogController@index');
    Route::get('operation-logs/modules', 'App\Http\Controllers\Admin\OperationLogController@modules');
    Route::get('operation-logs/{id}', 'App\Http\Controllers\Admin\OperationLogController@show');
    Route::delete('operation-logs/{id}', 'App\Http\Controllers\Admin\OperationLogController@destroy');
    Route::delete('operation-logs', 'App\Http\Controllers\Admin\OperationLogController@clear');

    Route::get('ip-blacklist', 'App\Http\Controllers\Admin\IpBlacklistController@index');
    Route::post('ip-blacklist', 'App\Http\Controllers\Admin\IpBlacklistController@store');
    Route::delete('ip-blacklist/{id}', 'App\Http\Controllers\Admin\IpBlacklistController@destroy');
    Route::delete('ip-blacklist/batch', 'App\Http\Controllers\Admin\IpBlacklistController@batchDelete');

    Route::get('settings', 'App\Http\Controllers\Admin\SettingController@index');
    Route::post('settings', 'App\Http\Controllers\Admin\SettingController@store');
    Route::get('settings/group/{group}', 'App\Http\Controllers\Admin\SettingController@getByGroup');
    Route::put('settings/group/{group}', 'App\Http\Controllers\Admin\SettingController@updateByGroup');
    Route::get('settings/{key}', 'App\Http\Controllers\Admin\SettingController@show');
    Route::put('settings/{key}', 'App\Http\Controllers\Admin\SettingController@update');

    Route::get('products', 'App\Http\Controllers\Admin\ProductController@index');
    Route::get('products/{id}', 'App\Http\Controllers\Admin\ProductController@show');
    Route::post('products', 'App\Http\Controllers\Admin\ProductController@store');
    Route::put('products/{id}', 'App\Http\Controllers\Admin\ProductController@update');
    Route::delete('products/{id}', 'App\Http\Controllers\Admin\ProductController@destroy');
    Route::delete('products/batch', 'App\Http\Controllers\Admin\ProductController@batchDestroy');
    Route::get('products/categories', 'App\Http\Controllers\Admin\ProductController@categories');
    Route::post('products/categories', 'App\Http\Controllers\Admin\ProductController@categoryStore');
    Route::put('products/categories/{id}', 'App\Http\Controllers\Admin\ProductController@categoryUpdate');
    Route::delete('products/categories/{id}', 'App\Http\Controllers\Admin\ProductController@categoryDestroy');

    Route::get('notification-templates', 'App\Http\Controllers\Admin\NotificationTemplateController@index');
    Route::get('notification-templates/{id}', 'App\Http\Controllers\Admin\NotificationTemplateController@show');
    Route::post('notification-templates', 'App\Http\Controllers\Admin\NotificationTemplateController@store');
    Route::put('notification-templates/{id}', 'App\Http\Controllers\Admin\NotificationTemplateController@update');
    Route::delete('notification-templates/{id}', 'App\Http\Controllers\Admin\NotificationTemplateController@destroy');
});
