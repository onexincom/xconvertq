<?php

use Illuminate\Support\Arr;
use Illuminate\Container\Container;

if (! function_exists('app')) {
    function app($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }
        return Container::getInstance()->make($abstract, $parameters);
    }
}

if (! function_exists('config')) {
    function config($key = null)
    {
       if (!app()->has('config')) {
           $config = require bathPath() . '/config/config.php';
           app()->instance('config', $config);
           return Arr::get($config, $key);
       }
       $config = app('config');
       if (is_null($key)) {
           return $config;
       }
       return Arr::get($config, $key);
    }
}

if (! function_exists('bathPath')) {
    function bathPath()
    {
        return realpath(__DIR__ . '/../../');
    }
}

if (! function_exists('checkIp')) {
    function checkIp($ip) {
        if(filter_var($ip, FILTER_VALIDATE_IP)) {
            return true;
        }else {
            return false;
        }
    }
}