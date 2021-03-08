<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
//require_once __DIR__ . '../../Authenticate.php';


/*
 * Authentication Example
 *
 * Signature of the 'buildAuthenticatedByPassword' method is 'clientId, secret, username, password'
 *
 * You need to create a connection on the PIM UI to get the credentials
 * */
$clientBuilder = new \Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientBuilder(
    'https://theakademy-serenity-1.cloud.akeneo.com'
);

$client = $clientBuilder->buildAuthenticatedByPassword(
    '4_49hj4k97d4cgwcsgoowcs4o4gs0s4gs4c04o04g8o0kkowcw0s',
    'do5js603t5skw80cck0skwcskc84occ84cwswkwgo44oogso8',
    'leo_test_6314',
    'b5e0e1f15'
);

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
 * We'll do a product API call and filter on 1 sku and populate the csv file with those information.
 * The CSV file will follow Akeneo's standard. To get an example, you can use the  "Product Export" functionality of
 * the PIM and export the product.
 *
 * */
# ------------------------------------------------------------------------------------------------------------
/*
 * Let's start by identifying the product properties and put them into an array. Because we will treat them differently,
 * we'll differentiate the properties with scalar value (identifier, enabled, family, parent) and the ones that are
 * objects and arrays.
 *
 * */

# Akeneo's API only returns attribute with data

# TIP 1: Create arrays of headers for your CSV to easily find your product properties and product attributes
$productPropertiesStrings   = ["identifier", "enabled", "family", "parent"];
$productPropertiesArrays   = ["categories", "groups"];
$productAssociations = ["associations", "quantified_associations"];

$productJsonString = file_get_contents("./code-correction/export_products/iphone_product_API_Akeneo.json");
$productJson = json_decode($productJsonString, true);

#Let's work on product properties
$csvHeaderAttributes = array_merge($productPropertiesStrings, $productPropertiesArrays);
$productData = [];

foreach($productPropertiesStrings as $prop) {
    $productData[] = $productJson[$prop];
}
foreach ($productPropertiesArrays as $prop) {
    $productData[] = implode(",", $productJson[$prop]);
}

$handle = fopen(" code-correction/export_products/Steps/Part_2/output.csv", "w");

foreach( $productJson["values"] as $attributeCode => $productValue ) {

    foreach ( $productValue as $prodV ) {

        $currentAttribute = $attributeCode;
        $currentProductValue = "";

        if ( $prodV["scope"] !== NULL ) {
            $currentAttribute .= "-" . $prodV["scope"];
        }
        if ( $prodV["locale"] !== NULL ) {
            $currentAttribute .= "-" . $prodV["locale"];
        }

        if(gettype($prodV["data"]) === 'string') {
            $currentProductValue = $prodV["data"];
        }
        if ( gettype($prodV["data"]) === 'boolean' ) {
            $currentProductValue = $prodV["data"] ? "1" : "0";
        }

        else if ( gettype($prodV["data"]) === 'array' ) {

            if ( isset($prodV["data"][0]) && isset($prodV["data"][0]["currency"])) { # it is a price
                $currentAttributePrice = $currentAttribute;

                foreach ($prodV["data"] as $price) {
                    $currentAttributePrice = $currentAttribute . "-" . $price["currency"];
                    $productData[] = $price["amount"];
                    $csvHeaderAttributes[] = $currentAttributePrice;
                }
                continue;
            }
            else if (isset($prodV["data"]["unit"])) { # it's a measure attribute
                $currentAttribute .= "-" . $prodV["data"]["unit"];
                $csvHeaderAttributes[] = $currentAttribute;
                $productData[] = $prodV["data"]["amount"];

                continue;
            }
            else { # it's an 1 dimensional array (multi-select, multiple reference entities, asset collection)
                $currentProductValue = implode(",", $prodV["data"]);
            }
        }

        $csvHeaderAttributes[] = $currentAttribute;
        $productData[] = $currentProductValue;

    }
}

fputcsv($handle, $csvHeaderAttributes, ";", "\"");
fputcsv($handle, $productData, ";", "\"");



/*
 * To work with multiple products, we can use this function that will prepend our CSV headers to the file before finishing
 * This way, we ensure all the attributes are in the headers
 *  */

function prepend($csvHeaderString, $orig_filename) {
    $context = stream_context_create();
    $orig_file = fopen($orig_filename, 'r', 1, $context);

    $temp_filename = tempnam(sys_get_temp_dir(), 'php_prepend_');
    file_put_contents($temp_filename, $csvHeaderString);
    file_put_contents($temp_filename, $orig_file, FILE_APPEND);

    fclose($orig_file);
    unlink($orig_filename);
    rename($temp_filename, $orig_filename);
}

/*
 * To fill arrays with empty string */

/*
    $arra = [];
    $arra[3] = "yes";

    $max = max(array_keys($arra));

    for($i = 0; $i < $max; $i++) {
        if (isset($arra[$i])) continue;

        $arra[$i] = "";
    }

    ksort($arra);
*/

