<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$clientBuilder = new \Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientBuilder('127.0.0.1:8080');
$client = $clientBuilder->buildAuthenticatedByPassword(
    '1_4cqwtcseom4g0wsccsk008kgww8wkokgscc0cs448kss4ocgc8',
    '220j6knit3j4gkgs8oo4k0ss4k0cwwgs880s00gwo4k0c4kok0',
    'workshop_import_product_8521',
    '67f11ed52'
);

/*
 * Plan of the workshop
 *
 * Introduction:
 *      Order of importing entities is Important.
 *      List of endpoints on api.akeneo.com or /v1/api/
 *      api php client EE
 *      When creating an product we use the attribute code! mapping might have to be done when doing this with real data
 *      user rights = go in the PIM and look at the permission tab of roles
 *      performances: API is a focus for 4.0 and import's performances have been greatly (4X) since 3.2
 *
 * 1- simple import 1 product
 *      We make them create the authenticate function, and the import product function
 *      If they've done the export workshop first, ask the attendees to query a product and save it in a array
 *      OR give them the example of product and ask them to create a product from the API
 *
 * 2- talk about error handling, what to do with them? log them into a file? raise exception? send email?
 *      HTTP exception - The parent Class, Two types of exception inherit from this exception: server exception and client exception
 *      Server exception - 5XX family, server failed to fulfill an apparently valid request, from Akeneo\Pim\Exception\ServerErrorHttpException
 *      Client exception - 4XX - ,
 *                          400 BAD REQUEST EXCEPTION, the request does not contain valid JSON (should not occure with the api-client)
 *                          401 Unauthorized: when you don't have the permission to access the resource
 *                          404 not found
 *                          UNPROCESSABLE ENTITY : business rules validation failures
 *
 * 3- Product media -
 *      Take image
 *      Get attributes from API, and filter on their type
 *
 * 4- Multiple products with multiple calls (1 per product)
 *
 * 5- Multiple products creation (with 1 call)
 *      - Mention that the response you get form the server will be an iterable object
 *      on wich we'll have to loop to get the information errors
 *      - There is a limit on the maximum number of products that you can upsert in one
 *      time on server side. By default this limit is set to 100.
 *
 * 6- Parallel calls (script that launches multiple time the method importMultipleProducts)
 *
 * */

$startTime = microtime(true);

//# Import 1 Product
//ImportOneProduct($client);
//
//# Import products media
//importMediaProducts($client);
//
//# Import multiple products
//foreach (range(10) as $e) {
//    ImportOneProduct($client);
//}
//
//ImportMultipleProducts($client);

importOneProductModel($client);

echo "executed in : " . processTime($startTime)." seconds \n";

function importOneProductModel($client)
{
    $productModel = json_decode(file_get_contents('/srv/pim/code-correction/ProductImport/product_model.json'), true);
    $code = $productModel["code"];
    unset ($productModel["code"]);

    try{
        $client->getProductModelApi()->upsert($code, $productModel);
    } catch (\Akeneo\Pim\ApiClient\Exception\UnprocessableEntityHttpException $e) {
        echo "Unprocessable\n";
        echo $e->getMessage();
        foreach ($e->getResponseErrors() as $error) {
            var_dump($error);
            echo $error['property'] ."\n";
            echo $error['message']."\n";
        }
    } catch (\Akeneo\Pim\ApiClient\Exception\UnauthorizedHttpException $e) {
        echo "Unauthorized\n";
    } catch (\Akeneo\Pim\ApiClient\Exception\NotFoundHttpException $e) {
        echo "Not Found\n";
    } catch (Akeneo\Pim\ApiClient\Exception\ServerErrorHttpException $e) {
        if (is_iterable($e->getMessage())) {
            foreach($e->getMessage() as $error) {
                var_dump($error);
            }
        } else {
            var_dump($e->getResponse());
        }
    }
}

/*
 * Importing one product
 * */
function importOneProduct($client)
{
    $productToImport = [
        "family"     => "accessories",
        "parent"     => null,
        "enabled"    => true,
        "values"     => [
            "ean"    => [["locale" => null, "scope" => null, "data" => "sds3423sd"]],
            "name"   => [["locale" => null, "scope" => null, "data" => "REMY_MACBOOKPRO"]],
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
        $response = $client->getProductApi()->create('REMY_MCBOOKPRO', $productToImport);
    } catch (\Akeneo\Pim\ApiClient\Exception\UnprocessableEntityHttpException $e) {
        echo "Unprocessable\n";
        echo $e->getMessage();
        foreach ($e->getResponseErrors() as $error) {
            echo $error['property'] ."\n";
            echo $error['message']."\n";
        }
    } catch (\Akeneo\Pim\ApiClient\Exception\UnauthorizedHttpException $e) {
        echo "Unauthorized\n";
    } catch (\Akeneo\Pim\ApiClient\Exception\NotFoundHttpException $e) {
        echo "Not Found\n";
    } catch (Akeneo\Pim\ApiClient\Exception\ServerErrorHttpException $e) {
        if (is_iterable($e->getMessage())) {
            foreach($e->getMessage() as $error) {
                var_dump($error);
            }
        } else {
            var_dump($e->getMessage());
        }
    }
    if (isset($response) && is_iterable($response)) {
        foreach($response as $r){
            var_dump($r);
        }
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
            ["identifier" => "cap23", "attribute" => "image", "scope" => null, "locale" => null]
        );
    } catch (\Akeneo\Pim\ApiClient\Exception\UnprocessableEntityHttpException $e) {
        echo "Unprocessable\n";
        var_dump($e->getMessage());
    }

}


function importMultipleProducts($client)
{
    try{
        $response = $client->getProductApi()->upsertList([
            [
                "identifier" => "cap",
                "family" => "accessories",
            ],
            [
                "identifier" => "cap23",
                "family" => "accessories",
                "values" => [
                    "auto_focus_points" => [
                        [
                            "scope"  => null,
                            "locale" => null,
                            "data"   => 2
                        ]
                    ]
                ]
            ],
            [
                "identifier" => "cap03",
                "family" => "accessories",
                "values" => [
                    "auto_focus_points" => [
                        [
                            "scope"  => null,
                            "locale" => null,
                            "data"   => 2
                        ]
                    ]
                ]
            ]
        ]);

    } catch (\Akeneo\Pim\ApiClient\Exception\UnprocessableEntityHttpException $e) {
        echo "Unprocessable\n";
        foreach ($e->getResponseErrors() as $error) {
            echo $error['property'] ."\n";
            echo $error['message']."\n";
        }
    } catch (\Akeneo\Pim\ApiClient\Exception\UnauthorizedHttpException $e) {
        echo "Unauthorized\n";
    } catch (\Akeneo\Pim\ApiClient\Exception\NotFoundHttpException $e) {
        echo "Not Found\n";
    } catch (Akeneo\Pim\ApiClient\Exception\ServerErrorHttpException $e) {
        if (is_iterable($e->getMessage())) {
            foreach($e->getMessage() as $error) {
                var_dump($error);
            }
        } else {
            var_dump($e->getMessage());
        }
    }
    if (is_iterable($response)) {
        foreach($response as $r){
            var_dump($r);
        }
    }
}

function processTime($startTime)
{
    $endTime = microtime(true);
    $totTime = round($endTime - $startTime, 3);
    return $totTime;
}



