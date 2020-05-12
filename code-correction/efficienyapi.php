<?php

use Akeneo\Pim\ApiClient\Exception\NotFoundHttpException;
use Akeneo\Pim\ApiClient\Exception\UnprocessableEntityHttpException;

require_once __DIR__ . '/../vendor/autoload.php';

$clientBuilder = new \Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientBuilder('http://localhost:8080/');
$client = $clientBuilder->buildAuthenticatedByPassword('1_cwq66ev2dk8owckg4sowccwcwksc0o44wwkwo8sg4wkw84g88', '36bzbnlzu4iscw0w88gc84c0c800okss0ogoos4k4sgwkg4ww8', 'admin', 'admin');

$rawFamilies = $client->getFamilyApi()->all();
$families = [];
foreach ($rawFamilies as $family) {
    $families[$family["code"]] = [
        "labels" => $family["labels"],
    ];
}

$rawCategories = $client->getCategoryApi()->all();
$categories = [];
foreach ($rawCategories as $category) {
    $categories[$category["code"]] = [
        "parent" => $category["parent"],
        "labels" => $category["labels"],
    ];
}

$searchBuilder = new \Akeneo\Pim\ApiClient\Search\SearchBuilder();
$searchBuilder->addFilter('enabled', '=', true);
$searchFilters = $searchBuilder->getFilters();


$products = $client->getProductApi()->all(100, ['search' => $searchFilters]);

function convertProduct($product)
{
    global $families;
    $convertedProduct = [
        'sku' => $product['identifier'],
        'categories-labels-fr' => '',
        'family-label-fr' => $families[$product['family']]['labels']['fr_FR'],
        'description-fr_FR-ecommerce' => '',
    ];

    if (isset($product['categories'])) {
        global $categories;

        $categoriesLabels = [];
        foreach ($product['categories'] as $category) {
            $categoriesLabels[] = $categories[$category]['labels']['fr_FR'];
        }

        $convertedProduct['categories-labels-fr'] = implode(',', $categoriesLabels);
    }

    if (isset($product['values']['description'])) {
        foreach ($product['values']['description'] as $description) {
            if ('fr_FR' === $description['locale'] && 'ecommerce' === $description['scope']) {
                $convertedProduct['description-fr_FR-ecommerce'] = $description['data'];
            }
        }
    }

    return $convertedProduct;
}

$fp = fopen('file.csv', 'w');

$headers = ['sku', 'categories-labels-fr', 'family-label-fr', 'description-fr_FR-ecommerce'];
fputcsv($fp, $headers, ';');

foreach ($products as $product) {
    $product = convertProduct($product);
    fputcsv($fp, $product, ';');
    echo memory_get_usage(true)/1024/1024 . "\n";
}

fclose($fp);
