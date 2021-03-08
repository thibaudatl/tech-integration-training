<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$clientBuilder = new \Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientBuilder('https://training-api.dev.cloud.akeneo.com');
$client = $clientBuilder->buildAuthenticatedByPassword(
    '9_1s327beliutcsc0s44woswcgwwwo8g8wsg400g8ok4cg0csoss',
    '20fra182299cc8w4c4k4o0gsc8g4k8ssk48gogw0wk8c444kgk',
    'leoconnection_5854',
    '3886f85c9'
);

# Return PHP iterator, that request next page everytime it gets to the end of the current page
$response = $client->getProductApi()->all();

foreach ($response as $resp) {
    # iterate through all the products
}

# Search filters (THIS CODE DOES NOT WORK)
$searchBuilder = new \Akeneo\Pim\ApiClient\Search\SearchBuilder();
$searchBuilder->addFilter("<PROPERTY>", "<OPERATOR>", "<VALUE>");
$searchFilters = $searchBuilder->getFilters();

$response = $client->getProductApi()->all("<PAGE_SIZE (default 10)>", [
    "search" => $searchFilters,
    "<QUERY_PARAMETER>" => "<VALUE_QUERY_PARAMETER>"
]);



/*
 * Write to a csv file in PHP
 *
 * */
    $handle = fopen( "<PATH_OF_THE_FILE>", "w" );  # fopen with "w" mode will create the file if it doesn't exists

    fputcsv( $handle, "<ARRAY_REPRESENTING_THE_CSV_ROW>", ";") ; # fputcsv write 1 line to the csv file

    fclose( $handle );   # fclose close the file




fputcsv( $handle, ["sku", "storage", "categories"], ";" );
$searchBuilder = new \Akeneo\Pim\ApiClient\Search\SearchBuilder();
$searchBuilder->addFilter("updated", "SINCE LAST N DAYS", 0);

$searchFilters = $searchBuilder->getFilters();

$response = $client->getProductApi()->all(10, [ "search" => $searchFilters ]);


$catLabels = "";

foreach ($response as $resp) {
    $catCode = implode(",", $resp["categories"]);

    foreach ($resp["categories"] as $cat) {
        $currentCat = $client->getCategoryApi()->get($cat);
        $catLabel   = $currentCat["labels"]["en_US"];
        $catLabels .= $catLabel . ",";
    }

    fputcsv( $handle, [$resp["identifier"], $resp["values"]["storage"][0]["data"], rtrim($catLabels, ",")], ";" );

}

fclose($handle);
