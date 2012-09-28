<?php

require_once __DIR__.'/lib/vendor/SplClassLoader.php';

$classLoader = new SplClassLoader('hafriedlander', __DIR__.'/lib');
$classLoader->register();
