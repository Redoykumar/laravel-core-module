<?php
use Illuminate\Support\Facades\Route;

Route::get('/core-api-test', function () {
    abort(501);
    return \Redoy\CoreModule\Facades\CoreResponse::successResponse(null,204);
});
