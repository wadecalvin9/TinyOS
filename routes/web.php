<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use GuzzleHttp\Cookie\FileCookieJar;
use Illuminate\Support\Facades\Storage;

Route::get(
    '/',
    [HomeController::class, 'fetch']
)->name('home');

Route::get('/browser', function () {
    return view('apps.browser');
})->name('browser');
Route::get('/youtube', function () {
    return view('apps.youtube');
})->name('youtube');
Route::get('/explorer', function () {
    return view('apps.fileexplorer');
})->name('explorer');


Route::get('/ftp-upload', [FileController::class, 'showForm'])->name('ftp.form');
Route::post('/save-to-local', [FileController::class, 'saveToLocal'])->name('save.local');
Route::post('/save-to-ftp', [FileController::class, 'saveToFtp']);
Route::get('/load-from-ftp', [FileController::class, 'loadFromFtp']);
Route::middleware('auth')->prefix('file-manager')->group(function () {
    Route::get('/list', [FileController::class, 'list']);
    Route::get('/load', [FileController::class, 'loadFromFtp']);
    Route::post('/save', [FileController::class, 'saveToFtp']);
    Route::post('/mkdir', [FileController::class, 'makeDir']);
    Route::post('/rename', [FileController::class, 'rename']);
    Route::delete('/delete', [FileController::class, 'delete']);
});
Route::get('/ftp-test', function () {
    return Storage::disk('ftp')->makeDirectory('test123')
        ? 'OK'
        : 'FAIL';
});

//settings-routes

Route::get('/settings', function () {
    return view('apps.settings.index');
})->name('settings.index');

Route::get('/settings/personalise', function () {
    return view('apps.settings.personalise');
})->name('settings.personalise');


Route::get('/settings/devices', function () {
    return view('apps.settings.devices');
})->name('settings.devices');


Route::get('/settings/network', function () {
    return view('apps.settings.network');
})->name('settings.network');


Route::get('/settings/privacy', function () {
    return view('apps.settings.privacy');
})->name('settings.privacy');


Route::get('/settings/about', function () {
    return view('apps.settings.about');
})->name('settings.about');


Route::put('/backround/change', [SettingsController::class, 'background'])->name('background.update');

///////////////////



///games


Route::get('/dbz', function () {
    return view('apps/games.dbz');
})->name('dbz');

///



///Auth


route::get('/register', function () {
    return view('auth.register');
})->name('auth.register.index');

route::post(
    '/auth/reg',
    [AuthController::class, 'register']
)->name('auth.register');

route::get('/login', function () {
    return view('auth.login');
})->name('auth.login.index');


route::post(
    '/auth/log',
    [AuthController::class, 'login']
)->name('auth.login');


route::post(
    '/auth/logout',
    [AuthController::class, 'logout']
)->name('auth.logout');

///
