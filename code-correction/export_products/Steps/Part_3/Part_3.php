<?php

# ------------------------------------------------------------------------------------------------------------
/*
 * Step 1 :
 *      - How can we organize our code so that we create a scalable and adaptable connector?
 *
 * In our previous example, we added the attribute "name", with the locale en_US. We hardcoded it.
 *
 * When developing a connector, we want to be able to parse the Product JSON and dynamically add the attribute values to
 * the csv. Let's try to do this with a single product. We'll first write the product properties and associations to the csv.
 *
 *
 * */
# ------------------------------------------------------------------------------------------------------------


$localePerChannel = $client->getChannelApi()->all();
$mapChannelLocale = [];

foreach ($localePerChannel as $channel) {
    $mapChannelLocale["code"] = [
        "currencies" => $mapChannelLocale["currencies"],
        "locales" =>  $mapChannelLocale["locales"]
    ];
}


$family = $productJson["family"];

# TIP 2: Query the family definition to get the attributes
$family = $client->getFamilyApi()->get($family);
$attributes =  $family["attributes"];


# TIP 3: query the definition of the attributes
# Explore the attribute object
$searchBuilder = new \Akeneo\Pim\ApiClient\Search\SearchBuilder();
$searchBuilder->addFilter("code", "IN", $attributes);
$searchFilters = $searchBuilder->getFilters();

$attributeConfiguration = $client->getAttributeApi()->all("100");

$attributeConfigurationArray = [];

## Here we will create an attribute array with the interesting data from the attribute endpoint
foreach ($attributeConfiguration as $att) {
    $attributeConfigurationArray[ $att["code"] ] = [
        "type"        => $att["type"] ,
        "localizable" => $att["localizable"],
        "scopable"    => $att["scopable"]
    ];
}