<?php

//require_once __DIR__ . '/../vendor/autoload.php';
//
//$clientBuilder = new \Akeneo\Pim\ApiClient\AkeneoPimClientBuilder('http://localhost/');
//$client = $clientBuilder->buildAuthenticatedByPassword('client_id', 'secret', 'admin', 'admin');

echo('Hello this is a basic example.');

$arra = [];
$arra[3] = "yes";
var_dump($arra[0]);
$max = max(array_keys($arra));

for($i = 0; $i < $max; $i++) {
    if (isset($arra[$i])) continue;

    $arra[$i] = "";
}

ksort($arra);
var_dump($arra);
