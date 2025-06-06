<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    if (Auth::guest()) {
        return redirect()->route('login');
    }
    $routeName = match ((int)Auth::user()->role_id) {
        5       => 'vistaAuditor',
        6       => 'vistaAuditor6',
        default => 'dashboard',
    };
    return redirect()->route($routeName);
})->name('home');

Route::middleware(['auth'])->group(function () {
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::middleware(['auth', 'verified', 'role:1,2,3,4'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('customers', 'customers.index')->name('customers.index');
    Volt::route('users', 'users.index')->name('users.index');
});

Route::middleware(['auth', 'role:5'])->group(function () {
    Route::view('vistaAuditor', 'vistaAuditor')->name('vistaAuditor');
});

Route::middleware(['auth', 'role:6'])->group(function () {
    Route::view('vistaAuditor6', 'vistaAuditor6')->name('vistaAuditor6');
});

require __DIR__.'/auth.php';
