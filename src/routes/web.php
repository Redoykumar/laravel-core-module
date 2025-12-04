<?php
use Illuminate\Support\Facades\Route;

Route::get('/core-test', function () {
    return \Redoy\CoreModule\Facades\CoreResponse::errorResponse(null, \Redoy\CoreModule\Constants\ApiCodes::BAD_REQUEST);
});
