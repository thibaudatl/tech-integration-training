<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
//require __DIR__ . '../../Authenticate.php';

/*
 * Authentication Example
 *
 * Signature of the 'buildAuthenticatedByPassword' method is 'clientId, secret, username, password'
 *
 * You need to create a connection on the PIM UI to get the credentials
 * */
$clientBuilder = new \Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientBuilder('https://training-api.dev.cloud.akeneo.com');
$client = $clientBuilder->buildAuthenticatedByPassword(
    '9_1s327beliutcsc0s44woswcgwwwo8g8wsg400g8ok4cg0csoss',
    '20fra182299cc8w4c4k4o0gsc8g4k8ssk48gogw0wk8c444kgk',
    'leoconnection_5854',
    '3886f85c9'
);


# ------------------------------------------------------------------------------------------------------------
/*
 * Step 5 :
 *      - How can we organize our code so that we create a scalable and adaptable connector?
 *
 * In our previous example, we added the attribute "name", with the locale en_US. We hardcoded it.
 *
 * When developing a connector, we want to be able to parse the Product JSON and dynamically add the attribute values to
 * the csv. Let's try to do this with a single product. We'll add all the attribute values from 1 product inside the csv.
 *
 *
 * */
# ------------------------------------------------------------------------------------------------------------

# TIP 1: Create arrays of headers for your CSV to easily find your product properties and product attributes
$productProperties   = ["identifier", "enabled", "family", "categories", "groups", "parent"];
$productAssociations = ["associations", "quantified_associations"];

# TIP 2: Query the family definition to get the attributes
$family = $client->getFamilyApi()->get("<YOUR_FAMILY_CODE>");
$attributes = $family["attributes"];

# TIP 3: query the definition of the attributes
$searchBuilder = new \Akeneo\Pim\ApiClient\Search\SearchBuilder();
$searchBuilder->addFilter("code", "IN", [$attributes]);
$searchFilters = $searchBuilder->getFilters();

$attributeConfiguration = $client->getAttributeApi()->all("100", [ "search" => $searchFilters ]);

$attributeConfigurationArray = [];

foreach ($attributeConfiguration as $att) {
    $attributeConfigurationArray[ $att["code"] ] = [
        "type"        => $att["type"] ,
        "localizable" => $att["localizable"],
        "scopable"    => $att["scopable"]
    ];
}




$product = $client->getProductApi()->get("<YOUR_PRODUCT_IDENTIFIER>");


