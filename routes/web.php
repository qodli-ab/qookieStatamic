<?php

use Illuminate\Support\Facades\Route;
use Qodli\QookieStatamic\Http\Controllers\ScriptController;

Route::get('/qookieqloud/script', ScriptController::class)->name('qookie-statamic.script');
