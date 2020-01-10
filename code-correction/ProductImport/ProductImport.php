<?php

require_once __DIR__ . '/../../vendor/autoload.php';



Class ProductImport
{
    protected $client;

    protected $clientBuilder;

    public function __construct()
    {
        $this->clientBuilder = new \Akeneo\Pim\ApiClient\AkeneoPimClientBuilder('127.0.0.1:8088');
        $this->client = $this->authenticate();
    }

    private function authenticate()
    {
        return $this->clientBuilder->buildAuthenticatedByPassword(
            '2_28q0eh5zrvgggscgkgs4kwk8gcsc4sw8wgwg8c0sks8gggw0s4',
            '4yyftxudoesc4wk88oc4sowkw0k4gs4c4oco444cs0ksckkw0c',
            'admin',
            'admin'
        );
    }


    public function ImportOneProduct()
    {
        $productToImport = [
            "family"     => "accessories",
            "parent"     => null,
            "categories" => ["print_accessories", "supplier_zaro"],
            "enabled"    => true,
            "values"     => [
                "ean"    => [["locale" => null, "scope" => null, "data" => "12456543"]],
                "name"   => [["locale" => null, "scope" => null, "data" => "LEOOOOO_SKU"]],
                "weight" => [
                    [
                        "locale" => null,
                        "scope"  => null,
                        "data"   => ["amount" => "500.0000", "unit" => "GRAM"]
                    ]
                ]
            ]
        ];

        try {
            $this->client->getProductApi()->create('NEW_SKU13', $productToImport);
        } catch (\Akeneo\Pim\ApiClient\Exception\UnprocessableEntityHttpException $e) {
            // do your stuff with the exception
            $requestBody = $e->getRequest()->getBody();
            $responseBody = $e->getResponse()->getBody();
            $httpCode = $e->getCode();
            $errorMessage = $e->getMessage();
            $errors = $e->getResponseErrors();
            foreach ($e->getResponseErrors() as $error) {
                // do your stuff with the error
                echo $error['property'];
                echo $error['message'];
            }
        } catch (HttpException $e) {
            // do your stuff with the exception
            $requestBody = $e->getRequest()->getBody();
            $responseBody = $e->getResponse()->getBody();
            $httpCode = $e->getCode();
            $errorMessage = $e->getMessage();
        }
    }
}

$productImport = new ProductImport();

$productImport->ImportOneProduct();