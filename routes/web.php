<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Middleware\CheckIsLogged;
use App\Http\Middleware\CheckIsNotLogged;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;

// Rota 100% Pública (Fora dos middlewares)
Route::get('/nota/{public_id}', [MainController::class, 'publicNote'])->name('public.note');

// auth routes - user not logged
Route::middleware([CheckIsNotLogged::class])->group(function(){
    Route::get('/login', [AuthController::class, 'login']);
    Route::post('/loginSubmit', [AuthController::class, 'loginSubmit']);
});

// app routes - user logged
Route::middleware([CheckIsLogged::class])->group(function(){
    Route::get('/', [MainController::class, 'index'])->name('home');
    Route::get('/newNote', [MainController::class, 'newNote'])->name('new');
    Route::post('/newNoteSubmit', [MainController::class, 'newNoteSubmit'])->name('newNoteSubmit');
    Route::get('/pinNote/{id}', [MainController::class, 'pinNote'])->name('pin');
    Route::get('/trash', [MainController::class, 'trash'])->name('trash');
    Route::get('/restoreNote/{id}', [MainController::class, 'restoreNote'])->name('restore');
    Route::get('/forceDeleteNote/{id}', [MainController::class, 'forceDeleteNote'])->name('forceDelete');
    Route::get('/exportPdf/{id}', [MainController::class, 'exportPdf'])->name('exportPdf');
    Route::post('/upload-image', [MainController::class, 'uploadImage'])->name('uploadImage');
    Route::post('/set-expiration', [MainController::class, 'setExpiration'])->name('setExpiration');
    Route::post('/update-order', [MainController::class, 'updateOrder'])->name('updateOrder');
    Route::post('/toggle-checklist', [MainController::class, 'toggleChecklist'])->name('toggleChecklist');
    Route::post('/unlock-note', [MainController::class, 'unlockNote'])->name('unlockNote');
    Route::post('/set-reminder', [MainController::class, 'setReminder'])->name('setReminder');

    // edit note
    Route::get('/editNote/{id}', [MainController::class, 'editNote'])->name('edit');
    Route::post('/editNoteSubmit', [MainController::class, 'editNoteSubmit'])->name('editNoteSubmit');
    // autosave
    Route::post('/autosave', [MainController::class, 'autosave'])->name('autosave');
    
    // delete note
    Route::get('/deleteNote/{id}', [MainController::class, 'deleteNote'])->name('delete');
    Route::get('/deleteNoteConfirm/{id}', [MainController::class, 'deleteNoteConfirm'])->name('deleteConfirm');

    // Categorias CRUD
    Route::prefix('categorias')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/salvar', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/{id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});