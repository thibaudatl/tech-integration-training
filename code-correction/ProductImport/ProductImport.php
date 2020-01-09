<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$clientBuilder = new \Akeneo\Pim\ApiClient\AkeneoPimClientBuilder('127.0.0.1:8088');
$client = $clientBuilder->buildAuthenticatedByPassword(
    '2_28q0eh5zrvgggscgkgs4kwk8gcsc4sw8wgwg8c0sks8gggw0s4',
    '4yyftxudoesc4wk88oc4sowkw0k4gs4c4oco444cs0ksckkw0c',
    'admin',
    'admin'
);

/*
 * Credentials for the API
 *
 * Client ID: 2_3xe8n56l3dq8ckggsw88sc8ggo08kcg8swc8w0ogwkokg4w40c
 * Secret: 6ngcps70r9oock8ss408g880ksssskgco0kc0w0k4cw8ccg0w
 * Username: productimport_2_3800
 * Password: 614e3555f
 *
*/

$searchBuilder = new \Akeneo\Pim\ApiClient\Search\SearchBuilder();
$searchBuilder->addFilter('enabled', '=', true);
$searchFilters = $searchBuilder->getFilters();

//$firstPage = $client->getProductApi()->listPerPage(50, true, ['search' => $searchFilters]);

$productToImport = [
    "family"     => "accessories",
    "parent"     => null,
    "categories" => ["print_accessories", "supplier_zaro"],
    "enabled"    => true,
    "values"     => [
        "ean"    => [["locale" => null, "scope" => null, "data" => "123456543"]],
        "name"   => [["locale" => null, "scope" => null, "data" => "Bag"]],
        "weight" => [
            [
                "locale" => null,
                "scope"  => null,
                "data"   => ["amount" => "500.0000", "unit" => "GRAM"]
            ]
        ]
    ]
];

try {
    $client->getProductApi()->create('NEW_SKU123', $productToImport);
} catch (\Akeneo\Pim\ApiClient\Exception\UnprocessableEntityHttpException $e) {
    // do your stuff with the exception
    $requestBody = $e->getRequest()->getBody();
    $responseBody = $e->getResponse()->getBody();
    $httpCode = $e->getCode();
    $errorMessage = $e->getMessage();
    $errors = $e->getResponseErrors();
    foreach ($e->getResponseErrors() as $error) {
        // do your stuff with the error
        echo $error['property'];
        echo $error['message'];
    }
}


