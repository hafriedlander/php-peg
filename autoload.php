<?php

require_once __DIR__.'/lib/vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';
$loader = new Symfony\Component\ClassLoader\UniversalClassLoader();
$loader->registerNamespace(
  'hafriedlander', array(
    __DIR__.'/lib',
  )
);
$loader->register();
