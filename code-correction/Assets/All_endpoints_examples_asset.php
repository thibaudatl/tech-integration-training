<?php

require_once __DIR__ . '/../vendor/autoload.php';

$clientBuilder = new \Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientBuilder('http://localhost:8080/');
$client = $clientBuilder->buildAuthenticatedByPassword(
    '1_cwq66ev2dk8owckg4sowccwcwksc0o44wwkwo8sg4wkw84g88',
    '36bzbnlzu4iscw0w88gc84c0c800okss0ogoos4k4sgwkg4ww8',
    'admin',
    'admin'
);

# Endpoints for assets

$familyAssetData = [
    'code' => 'book',
    'labels' => [
        'en_US' => 'Book',
        'fr_FR' => 'Livre',
    ]
];
$client->getAssetFamilyApi()->upsert('book', $familyAssetData);

$familyAssetAttributeNberPage = [
    "code" => "nber_page",
    "labels" => [
        "en_US" => "Number of pages",
        "fr_FR" => "Nombre de pages"
    ],
    "type" => "number",
    "value_per_locale" => false,
    "value_per_channel" => false,
    "is_required_for_completeness" => true
];


# Add Asset Attribute
$client->getAssetAttributeApi()->upsert("book", "nber_page", $familyAssetAttributeNberPage);

$familyAssetAttributeType = [
    "code" => "typeOfBook",
    "labels" => [
        "en_US" => "Type of book",
        "fr_FR" => "Type de livre"
    ],
    "type" => "single_option",
    "value_per_locale" => false,
    "value_per_channel" => false,
    "is_required_for_completeness" => true
];


# Add attribute on Asset family
$client->getAssetAttributeApi()->upsert("book", "typeOfBook", $familyAssetAttributeType);

$familyAssetAttributeTypeValueComics = [
    "code" => "comics",
    "labels" => [
        "en_US" => "Comic",
        "fr_FR" => "Comique"
    ]
];


# Add attribute option
$client->getAssetAttributeOptionApi()->upsert("book", "typeOfBook", "comics",$familyAssetAttributeTypeValueComics);

$familyAssetAttributeTypeValueThriller = [
    "code" => "Thriller",
    "labels" => [
        "en_US" => "Thriller",
        "fr_FR" => "Policier"
    ]
];
$client->getAssetAttributeOptionApi()->upsert("book", "typeOfBook", "Thriller",$familyAssetAttributeTypeValueThriller);

//saleforce.png

$mediaCodeComic = $client->getAssetMediaFileApi()->create("75-years-of-DC-comics.jpg");
$dataAsset = [
    "code" => "75YearOfComic",
    "values" => [
        "image" => [
            [
                "locale" => null,
                "channel" => null,
                "data" => $mediaCodeComic,
            ]
        ],
        "typeOfBook" => [
            [
                "locale" => null,
                "channel" => null,
                "data" => "comics"
            ]
        ],
        "nber_page" => [
            [
                "locale" => null,
                "channel" => null,
                "data" => "85"
            ]
        ]
    ]
];
$client->getAssetManagerApi()->upsert("book", "75YearOfComic", $dataAsset);
//var_dump($client->getAssetManagerApi()->get("book","75YearOfComic"));


$dataAssetCollection =
    [
        "code" => "book_asset_collection",
        "type" => "pim_catalog_asset_collection",
        "localizable" => false,
        "scopable" => false,
        "group" => "marketing",
        "labels" => [
            "en_US" => "My new books assets",
            "fr_FR" => "Mes nouveaux livres numériques"
        ],
        "reference_data_name" => "book"
    ];
$violation  = $client->getAttributeApi()->upsert("book_asset_collection", $dataAssetCollection);
var_dump($violation);


$violation  = $client->getAttributeApi()->get("book_asset_collection");
var_dump($violation);

$violation  = $client->getFamilyApi()->get("accessories");
var_dump($violation);

// create family with new attribute
$dataAttributeAssetCollection =  [
    'attributes'             => ['sku', 'name', 'book_asset_collection'],
    'attribute_as_label'     => 'name',
    'attribute_as_image'     => 'book_asset_collection',
    'labels'                 => [
        'en_US' => 'Books',
        'fr_FR' => 'Livres',
    ]
];
$violation  = $client->getFamilyApi()->upsert("books",$dataAttributeAssetCollection);
var_dump($violation);
