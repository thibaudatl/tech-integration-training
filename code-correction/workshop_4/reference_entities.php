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

// those attributes code can change, make sure they match your environment
$refEntityCode = "chemical_compound";
$attributeCode = "chemical_compounds";

$searchBuilderEntity = new \Akeneo\Pim\ApiClient\Search\SearchBuilder();
$searchBuilderEntity->addFilter("updated", "SINCE LAST N DAYS", "1");

$refEntities = $client->getReferenceEntityRecordApi()->all($refEntityCode, [
    "search" =>  $searchBuilderEntity->getFilters()
]);

/* Array of Hazardous compounds reference enitty record codes */
$newHazardousArray = [];

foreach ($refEntities as $key => $record)
{
    if( $record["values"]["hazardous"][0]["data"] === "hazardous" ){
        if (!in_array($record["values"]["hazardous"][0]["data"], $newHazardousArray)){
            array_push($newHazardousArray, $record["values"]["hazardous"][0]["data"]);
        }
        // add to array
    }else{
        // check if in existing array and pop if yes
    }
}

$searchBuilderProduct =  new \Akeneo\Pim\ApiClient\Search\SearchBuilder();
$searchBuilderEntity->addFilter("updated", "SINCE LAST N DAYS", "1");
$searchBuilderEntity->addFilter("family", "IN", ["nail_polisher"]);
// ... other filters

$products = $client->getProductApi()->all("100", [
    "search" => $searchBuilderProduct->getFilters()
]);

$productsCount = 0;
$currentArrayProductsUpdated = [];

foreach ($products as $product) {
    $productWHazardousFlag = [];

    $hazardFlag = udpateBoolean($product["values"][$attributeCode][0]["data"], $newHazardousArray);

    if($hazardFlag === $product["values"]["hazardous"][0]["data"])

    $productWHazardousFlag[$productsCount] =
        [
            "identifier" => $product["identifier"],
            "values" => [
                $attributeCode => [
                    [
                        "locale" => null,
                        "scope"  => null,
                        "data"   => ""
                    ]
                ]
            ]
        ];

    $currentArrayProductsUpdated[] = $productWHazardousFlag;

    if( $productsCount % 99 === 0 )
    {
//        $response = patchBatchProducts($currentArrayProductsUpdated, $client);
//        $currentArrayProductsUpdated = [];

    }
    $productsCount++;
}


function isProductDangerous($listChemString, $newHazardousArray)
{
    $listChemArray = explode(",", $listChemString);

    foreach($listChemArray as $compound) {
        if (in_array($compound, $newHazardousArray)) {
            return true;
        }
    }

    return false;
}