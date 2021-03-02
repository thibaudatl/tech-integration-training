<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$clientBuilder = new \Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientBuilder('http://localhost:8080');
$client = $clientBuilder->buildAuthenticatedByPassword(
    '5_59cm7oo30cg04gcsog8so0cs00sosc4kg0o40s40c0s8wgocgg',
    '30yk18mt51us8oscoggk0cgog00soks0s40osw0c04skowwwo4',
    'leo_9307',
    'a82a66150'
);

/*
 * Plan of the workshop
 *
 * Introduction:
 *      Order of importing entities is Important.
 *      List of endpoints on api.akeneo.com or /v1/api/
 *      api php client EE
 *      When creating a product we use the attribute code to populate attribute values. mapping might be needed when
 *      doing this with real data (code of attribute in ERP =/=
 *      user rights = go in the PIM and look at the permission tab of roles
 *      performances: API is a focus for 4.0 and import's performances have been greatly (4X) since 3.2
 *
 * 1- simple import 1 product
 *
 * 2- Error handling, what to do with them? log them into a file? raise exception? send email?
 *      HTTP exception - The parent Class, Two types of exception inherit from this exception: server exception and client exception
 *      Server exception - 5XX family, server failed to fulfill an apparently valid request, from Akeneo\Pim\Exception\ServerErrorHttpException
 *      Client exception - 4XX -
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

//$identifiers = ["A10143002056100","A10143002077100","C10016005030400","A10143002097100","A10142008000600","A10143001042100","A10143001040100","A10143001038100","A10143001036100","A10143001034100","A10142008000001","E10039005056000","A10142007000200","A10042003025400","E10039004056000","A10143001042450","A10143001040450","A10143001038450","A10143001036450","A10143001034450","E10039003056000","E10039002056000","A10142006000001","A10142006000600","A10042003024400","E10039001056000","A10142005000650","A10142004000001","A10142003000001","A10142002000001","A10142001000680","A10102012055001","A10102012056001","A10102012077001","A10102012097001","C10016005029400","F10002002000750","A10141030055100","A10141030056100","A10141030077100","A10141029055020","A10141029056020","A10141029077020","A10141029097020","A10042003023400","A10101023096020","A10101023055020","A10101023056020","A10101023077020","A10101023097020","C10016005028400","A10101002096020","A10101002055020","A10101002056020","A10101002077020","A10101002097020","A10101016096020","A10101016055020","A10101016056020","A10101016077020","A10101016097020","F30001003000710","A10101009096020","A10101009055020","A10101009056020","A10101009077020","A10101009097020","A10141028055001","A10141028056001","A10141028077001","A10141028097001","A10101023096500","A10101023055500","A10101023056500","A10101023077500","A10101023097500","A10101010096500","A10101010055500","A10101010056500","A10101010077500","A10101010097500","A10141027055001","A10141027056001","A10141027077001","A10101011096500","A10101011055500","A10101011056500","A10101011077500","A10101011097500","A10101023096001","A10101023055001","A10101023056001","A10101023077001","A10101023097001","A10101008096500","A10101008055500","A10101008056500","A10101008077500","A10101008097500","A10101007096500"];
//foreach ($identifiers as $i) {
//    try{
//        $reponse = $client->getProductApi()->get($i);
//    } catch (\Akeneo\Pim\ApiClient\Exception\UnprocessableEntityHttpException $e) {
//        echo "Unprocessable\n";
//        echo $e->getMessage();
//        foreach ($e->getResponseErrors() as $error) {
//            echo $error['property'] ."\n";
//            echo $error['message']."\n";
//        }
//    } catch (\Akeneo\Pim\ApiClient\Exception\UnauthorizedHttpException $e) {
//        echo "Unauthorized\n";
//    } catch (\Akeneo\Pim\ApiClient\Exception\NotFoundHttpException $e) {
//        echo "Not Found\n";
//    } catch (Akeneo\Pim\ApiClient\Exception\ServerErrorHttpException $e) {
//        if (is_iterable($e->getMessage())) {
//            foreach($e->getMessage() as $error) {
//                var_dump($error);
//            }
//        } else {
//            var_dump($e->getResponse());
//        }
//    }
//
//}
$startTime = microtime(true);

# Import 1 Product
//ImportOneProduct($client);
//
////# Import products media
//importMediaProducts($client);

# Import multiple products
//foreach (range(10) as $e) {
//    ImportOneProduct($client);
//}

ImportMultipleProducts($client);

//importOneProductModel($client);

endTimer($startTime);


/*
 * Importing one product
 * */
function importOneProduct($client)
{
    $productToImport = json_decode(file_get_contents('/srv/pim/code-correction/ProductImport/statics/product.json'), true);

    $identifier = bin2hex(random_bytes(16));

    $productToImport["identifier"] = $identifier;

    try {
        $response = $client->getProductApi()->create($identifier, $productToImport);
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

function importOneProductModel($client)
{
    $productModel = json_decode(file_get_contents('/srv/pim/code-correction/ProductImport/statics/product_model.json'), true);
    $code = $productModel["code"];
    unset ($productModel["code"]);

    try{
        $client->getProductModelApi()->upsert($code, $productModel);
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
            var_dump($e->getResponse());
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
    $file = fopen("/srv/pim/code-correction/ProductImport/statics/akeneo.png", 'r');

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
    }
}


function importMultipleProducts($client)
{
    $productToImport = json_decode(file_get_contents('/srv/pim/code-correction/import_products/statics/100_products.json'), true);

    try{
        $response = $client->getProductApi()->upsertList($productToImport);

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

function endTimer($startTime)
{
    $endTime = microtime(true);
    $totTime = round($endTime - $startTime, 3);
    echo "executed in : " . $totTime." seconds \n";
}


