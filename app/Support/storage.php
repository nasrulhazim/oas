<?php

if (! function_exists('oas_path')) {
    function oas_path($path = '')
    {
        return storage_path('oas/' . $path);
    }
}
