<?php

//autoload annotations
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    $loader = include __DIR__ . '/vendor/autoload.php';
}
if (isset($loader)) {
    \WooRefillDoctrine\Common\Annotations\AnnotationRegistry::registerLoader([$loader, 'loadClass']);
}


