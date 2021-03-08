<?php

require_once __DIR__ . '/../../vendor/autoload.php';

/*
 * Authentication
 * */
$clientBuilder = new \Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientBuilder(
    'https://training-api.demo.cloud.akeneo.com'
);
$client = $clientBuilder->buildAuthenticatedByPassword(
    '1_4im941xl6p6ow0oo4skossg48ogos8c88gscgcgssg80skg8c4',
    '14pj2fa30x28ccgscgwsskc480k44o0oskok8ogwkckw0gssow',
    'erp_1496',
    'a08063637'
);

$longDescAttribute = "long_description";
$shortDescAttribute = "truncated_long_description";

function buildFilters($longDescAttribute, $shortDescAttribute)
{
    $searchBuilder = new \Akeneo\Pim\ApiClient\Search\SearchBuilder();

    $searchBuilder->addFilter("family", "IN", ["smartphones"]);
    $searchBuilder->addFilter("updated", "SINCE LAST N DAYS", "1");
    $searchBuilder->addFilter( $shortDescAttribute, "EMPTY", "");
    $searchBuilder->addFilter( $longDescAttribute, "NOT EMPTY", "");

    $filters = [
        "search" => $searchBuilder->getFilters(),
        "search_locale" => "en_US",
        "attributes" => $longDescAttribute
    ];
    return $filters;
}

$filters = buildFilters($longDescAttribute, $shortDescAttribute);

$response = $client->getProductApi()->all(100, $filters );

$productsCount = 0;
$currentArrayProductsUpdated = [];

foreach ($response as $product) {
    $productWTruncatedDesc = [];

    $productWTruncatedDesc[$productsCount] =
        [
            "identifier" => $product["identifier"],
            "values" => [
                $shortDescAttribute => [
                    [
                        "locale" => "en_US",
                        "scope"  => null,
                        "data"   => truncateString($product["values"][$longDescAttribute][0]["data"])
                    ]
                ]
            ]
        ];

    $currentArrayProductsUpdated[] = $productWTruncatedDesc;

    if( $productsCount % 99 === 0 )
    {
//        $response = patchBatchProducts($currentArrayProductsUpdated, $client);
//        $currentArrayProductsUpdated = [];

    }
    $productsCount++;
}

var_dump($currentArrayProductsUpdated);
//$response = patchBatchProducts($currentArrayProductsUpdated, $client);


function truncateString($string)
{
    return substr($string, 0, 37) . "..." ;
}

function patchBatchProducts($products, $client)
{
    try {
        $response = $client->getProductApi()->upsertList($products);

    } catch (\Akeneo\Pim\ApiClient\Exception\UnprocessableEntityHttpException $e) {
        // Catch exceptions
    }
    foreach ($response as $validationItem)
    {
        if ($validationItem["status_code"] === 422 )
        {
            // Do something when the data wasn't processed
        }
    }

    return $response;
}






