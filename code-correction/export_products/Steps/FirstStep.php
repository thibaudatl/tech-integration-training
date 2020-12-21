<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

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

/*
 * Query endpoint with the api client
 *
 * returns a PHP iterator.
 * The Iterator requests the next page everytime it gets to the end of the current page
 *
 * Other endpoints available here: vendor/akeneo/api-php-client/src/AkeneoPimClientInterface.php
 * Full list for product methods here: vendor/akeneo/api-php-client/src/Api/ProductApi.php
 * */

$response = $client->getProductApi()->all();
# other methods available on product API include "create", "upsert", "delete" etc.. see links above
$response = $client->getCategoryApi()->all();

foreach ($response as $resp) {
    # iterate through all the entities retrieved
}

# Search filters (THIS CODE SERVES AS AN EXAMPLE)
$searchBuilder = new \Akeneo\Pim\ApiClient\Search\SearchBuilder();
$searchBuilder->addFilter("<PROPERTY>", "<OPERATOR>", "<VALUE>");
$searchFilters = $searchBuilder->getFilters();

$response = $client->getProductApi()->all("<PAGE_SIZE (default 10)>", [
    "search" => $searchFilters,
    "<QUERY_PARAMETER>" => "<VALUE_QUERY_PARAMETER>"
]);


# ------------------------------------------------------------------------------------------------------------
/*
 * Step 1:
 *      - Use the 'Search filters' example above to query products that were updated in the last day
 *      - Iterate over the results and print out the sku of the products
 *
 * hint: use the addFilter method, with : property = 'updated', operator = 'SINCE N LAST DAYS' & 'value' = 1
 * */
# ------------------------------------------------------------------------------------------------------------

$searchBuilder = new \Akeneo\Pim\ApiClient\Search\SearchBuilder();
$searchBuilder->addFilter("updated", "SINCE LAST N DAYS", 0);
$searchFilters = $searchBuilder->getFilters();

$response = $client->getProductApi()->all(10, [ "search" => $searchFilters ]);

foreach ($response as $product) {
    echo $product["identifier"];
}


# ------------------------------------------------------------------------------------------------------------
/*
 * Step 2 :
 *      - Modify your existing foreach to write the sku of each product inside a csv file,
 *        The first line of the CSV should be the header, with the column 'sku'
 *        Each other line should be the sku of a product
 *
 * E.g. : sku
 *        1238NGY
 *        iphone_123
 *        2162486
 * */
# ------------------------------------------------------------------------------------------------------------
#                           PHP HELP: Write to a csv file in PHP
# ------------------------------------------------------------------------------------------------------------
/*
 * There are 3 method we're going to look at: fopen, fputcsv & fclose. See below
 */
$handle = fopen( "<PATH_OF_THE_FILE>", "w" );  # fopen with "w" mode will create the file if it doesn't exists

fputcsv( $handle, "<ARRAY_REPRESENTING_THE_CSV_ROW>", ";") ; # fputcsv write 1 line to the csv file

fclose( $handle );   # fclose close the file
# ------------------------------------------------------------------------------------------------------------


$searchBuilder = new \Akeneo\Pim\ApiClient\Search\SearchBuilder();
$searchBuilder->addFilter("updated", "SINCE LAST N DAYS", 0);
$searchFilters = $searchBuilder->getFilters();

$response = $client->getProductApi()->all(10, [ "search" => $searchFilters ]);

$handle = fopen( "products.csv", "w" );
fputcsv( $handle, ["sku"], ";") ;

foreach ($response as $product) {
    fputcsv( $handle, [$product["identifier"]], ";") ;
}

fclose($handle);


# ------------------------------------------------------------------------------------------------------------
/*
 * Step 3 :
 *      - Modify your existing foreach to write the family & the categories of each product inside the csv file,
 *
 * E.g. : sku;        family;      categories
 *        1238NGY;    camcorder;   cameras, camcorders, new_product
 *        iphone_123; smartphones; phones
 *        2162486;    book;        ebook, on_sale
 *
 * The challenge of this step is on the data transformation of categories. You will receive an array of string: ['ebook,', 'on_sale']
 * That you have to transform into a string: 'ebook, on_sale'
 * */
# ------------------------------------------------------------------------------------------------------------
#          PHP HELP: Tranform an Array into a comma separated string with implode()
# ------------------------------------------------------------------------------------------------------------
# $myArray = ['ebook', 'onsale'];
# $commaSeparatedString = implode(",", $myArray);
# echo $commaSeparatedString;  #   OUTPUT: 'ebook,onsale'
# ------------------------------------------------------------------------------------------------------------

fputcsv( $handle, ["sku", "family", "categories"], ";") ;

foreach ($response as $product) {
    $stringCategories = implode(",", $product["categories"]);

    fputcsv( $handle, [$product["identifier"], $product["family"], $stringCategories], ";") ;
}


# ------------------------------------------------------------------------------------------------------------
/*
 * Step 4 :
 *      - Let's add the attribute "name" to our product export
 *
 * E.g. : [...]; categories;          name-en_US;
 *        [...]; cameras, camcorders; Microsoft LifeCam VX-2000;
 *        [...]; phones;              Apple iPhone 8 red - 64 Gb;
 *        [...]; ebook, on_sale;      Mistborn - The Final Empire;
 *
 * The attribute values are store in the "values" object of the JSON
 *
 * */
# ------------------------------------------------------------------------------------------------------------


fputcsv( $handle, ["sku", "family", "categories", "name-en_US"], ";") ;

foreach ($response as $product) {

    fputcsv( $handle,
        [
            $product["identifier"],
            $product["family"],
            $stringCategories,
            $product["values"]["name"][0]["data"]
    ], ";") ;
}

