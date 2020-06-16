<?php

require_once __DIR__.'/../vendor/autoload.php';

$clientBuilder = new \Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientBuilder('http://localhost:8080/');
$client = $clientBuilder->buildAuthenticatedByPassword(
    '1_cwq66ev2dk8owckg4sowccwcwksc0o44wwkwo8sg4wkw84g88',
    '36bzbnlzu4iscw0w88gc84c0c800okss0ogoos4k4sgwkg4ww8',
    'admin',
    'admin'
);
//create
$data["identifier"]= "Test new product";
$data["enabled"] = true;
$data["family"] = "Shoes";
$data["categories"] = ["office"];
$data["groups"] = [];
$data["parent"] = null;
$data["values"]  = [
    'description' => [
        [
        'locale' => 'en_US',
        'scope' => 'ecommerce',
        'data' => "new prod description"
        ]
    ]
];
$client->getProductApi()->upsert("Test new product", $data);

//update
$data["identifier"]= "Test new product rename";
$client->getProductApi()->upsert("Test new product", $data);

// delete
$client->getProductApi()->delete("Test new product");

//diff
$searchBuilder = new \Akeneo\Pim\ApiClient\Search\SearchBuilder();
$searchBuilder->addFilter('enabled', '=', true);

$now =  date('Y-m-d H:i:s');
$time   = strtotime($now);
$time   = $time - (60*60); //one hour
$beforeOneHour = date("Y-m-d H:i:s", $time);
$searchBuilder->addFilter('created', '>', $beforeOneHour);
$searchFilters = $searchBuilder->getFilters();
$products = $client->getProductApi()->all(100, ['search' => $searchFilters]);
foreach ($products as $product){
    echo $product['identifier'];
};

?>

