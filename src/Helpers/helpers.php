<?php
if (! function_exists('core_success')) {
    function core_success($data = [], $message = null) {
        return \Redoy\CoreModule\Facades\CoreResponse::successResponse($data, null, $message);
    }
}

if (! function_exists('core_error')) {
    function core_error($message = 'Error', $code = 400) {
        return \Redoy\CoreModule\Facades\CoreResponse::errorResponse(null, $code, $message);
    }
}
