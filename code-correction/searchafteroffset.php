<?php

require_once __DIR__.'/../vendor/autoload.php';

$clientBuilder = new \Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientBuilder('http://localhost:8080/');
$client = $clientBuilder->buildAuthenticatedByPassword(
    '1_cwq66ev2dk8owckg4sowccwcwksc0o44wwkwo8sg4wkw84g88',
    '36bzbnlzu4iscw0w88gc84c0c800okss0ogoos4k4sgwkg4ww8',
    'admin',
    'admin'
);
//offset

$queryParameters['pagination_type'] = 'page';
$result = $client->getProductApi()->listPerPage(10,true,$queryParameters);
foreach ($result->getItems() as $product){
    echo $product['identifier'];
    echo "\n\r";
}

//search after
$queryParameters['pagination_type'] = 'search_after';
$result = $client->getProductApi()->all(10,$queryParameters);
foreach($result as $product )
{
    var_dump( $product['identifier'] );
};
parse_str($result->getNextLink(),$outputArray);
$queryParameters['search_after'] = $outputArray['search_after'];
$result = $client->getProductApi()->listPerPage(1,true,$queryParameters);
foreach ($result->getItems() as $product){
    echo $product['identifier'];
    echo "\n\r";
}
