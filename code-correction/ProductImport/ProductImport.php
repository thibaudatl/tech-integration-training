<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$clientBuilder = new \Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientBuilder('127.0.0.1:8088');
$client = $clientBuilder->buildAuthenticatedByPassword(
    '2_28q0eh5zrvgggscgkgs4kwk8gcsc4sw8wgwg8c0sks8gggw0s4',
    '4yyftxudoesc4wk88oc4sowkw0k4gs4c4oco444cs0ksckkw0c',
    'admin',
    'admin'
);
try{
    $response = $client->getProductApi()->upsertList([
        [
            "identifier" => "34567",
            "values"     => [
                "ean" => [
                    [
                        "locale" => null,
                        "scope"  => null,
                        "data"   => "1092"
                    ]
                ]
            ],
            "family" => "accessories"
        ],
        [
            "identifier" => "3fg567",
            "values"     => [
                "ean" => [
                    [
                        "locale" => null,
                        "scope"  => null,
                        "data"   => "1092"
                    ]
                ],
                "family" => "accessories"
            ]
        ]
    ]);
} catch (\Akeneo\Pim\ApiClient\Exception\UnprocessableEntityHttpException $e) {
    // You are able to get information on what was the request, and why it failed
    $requestBody = $e->getRequest()->getBody();
    $responseBody = $e->getResponse()->getBody();
    $httpCode = $e->getCode();
    $errorMessage = $e->getMessage();
    var_dump($errorMessage);
    $errors = $e->getResponseErrors();
    foreach ($e->getResponseErrors() as $error) {
        // do your stuff with the error
        echo $error['property'];
        echo $error['message'];
    }
}


die;
/*
 * Plan of the workshop
 *
 * Introduction:
 *      Order of importing entities is Important.
 *      List of endpoints on api.akeneo.com or /v1/api/
 *      api php client EE
 *      When creating an product we use the attribute code! mapping might have to be done when doing this with real data
 *
 * 1- simple import 1 product
 *      We make them create the authenticate function, and the import product function
 *      If they've done the export workshop first, ask the attendees to query a product and save it in a array
 *      OR give them the example of product and ask them to create a product from the API
 *
 * 2- talk about error handling, what to do with them? log them into a file? raise exception?
 *      HTTP exception - The parent Class, Two types of exception inherit from this exception: server exception and client exception
 *      Server exception - 5XX family, server failed to fulfill an apparently valid request, from Akeneo\Pim\Exception\ServerErrorHttpException
 *      Client exception - 4XX - ,
 *                          400 BAD REQUEST EXCEPTION, the request does not contain valid JSON (should not occure with the api-client)
 *                          401 Unauthorized: when you don't have the permission to access the resource
 *                          404 not found
 *                          UNPROCESSABLE ENTITY
 *
 * 3- Product media -
 *      Take image
 *      Get attributes from API,
 *
 * 4- Multiple products with multiple calls (1 per product)
 *
 * 5- Multiple products creation (with 1 call)
 *
 * 6- Parallel calls (script that launches multiple time the method importMultipleProducts)
 *
 * */


# Import 1 Product
ImportOneProduct($client);

# Import products media
importMediaProducts($client);

# Import multiple products
foreach (range(10) as $e) {
    ImportOneProduct($client);
}

ImportMultipleProducts($client);



/*
 * Importing one product
 * */
function ImportOneProduct($client)
{
    $productToImport = [
        "family"     => "accessories",
        "parent"     => null,
        "categories" => ["print_accessories", "supplier_zaro"],
        "enabled"    => true,
        "values"     => [
            "ean"    => [["locale" => null, "scope" => null, "data" => "12456543"]],
            "name"   => [["locale" => null, "scope" => null, "data" => "LEOOOOO_SKU"]],
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
        $client->getProductApi()->create('NEW_SKU13', $productToImport);
    } catch (\Akeneo\Pim\ApiClient\Exception\UnprocessableEntityHttpException $e) {
        // You are able to get information on what was the request, and why it failed
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
    } catch (\Akeneo\Pim\ApiClient\Exception\HttpException $e) {
        // do your stuff with the exception
        $requestBody = $e->getRequest()->getBody();
        $responseBody = $e->getResponse()->getBody();
        $httpCode = $e->getCode();
        $errorMessage = $e->getMessage();
    }
}

/*
 * update a product and making it
 * Images must be on your local because we're sending the binary over
 * Product or product model must exists before adding an image
 * */
function importMediaProducts($client)
{
    $file = fopen("/srv/pim/code-correction/ProductImport/akeneo.png", 'r');

    if ($file === false){
        echo "the path of your image is wrong" . PHP_EOL;
        die;
    }

    try {
        $client->getProductMediaFileApi()->create(
            $file,
            ["identifier" => "FGHJ", "attribute" => "image", "scope" => null, "locale" => null]
        );
    } catch (\Akeneo\Pim\ApiClient\Exception\UnprocessableEntityHttpException $e) {
        echo "Unprocessable\n";
        var_dump($e->getMessage());
    }

}

function ImportMultipleProducts($client)
{

}



