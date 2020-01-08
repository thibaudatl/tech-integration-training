<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$clientBuilder = new \Akeneo\Pim\ApiClient\AkeneoPimClientBuilder('127.0.0.1:8088');
$client = $clientBuilder->buildAuthenticatedByPassword(
    '2_28q0eh5zrvgggscgkgs4kwk8gcsc4sw8wgwg8c0sks8gggw0s4',
    '4yyftxudoesc4wk88oc4sowkw0k4gs4c4oco444cs0ksckkw0c',
    'admin',
    'admin'
);

$token = $client->getToken();
var_dump($token);
/*
 * Credentials for the API
 *
 * Client ID: 2_3xe8n56l3dq8ckggsw88sc8ggo08kcg8swc8w0ogwkokg4w40c
 * Secret: 6ngcps70r9oock8ss408g880ksssskgco0kc0w0k4cw8ccg0w
 * Username: productimport_2_3800
 * Password: 614e3555f
 *
*/

$product = $client->getProductApi()->get('Tshirt-divided-blue-s');
var_dump($product["identifier"]);