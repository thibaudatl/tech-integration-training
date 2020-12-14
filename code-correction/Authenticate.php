<?php

namespace ApiTraining;

class Authenticate
{
    protected $clientID = '' ;
    protected $secret = '' ;
    protected $username = '' ;
    protected $password = '' ;

    public function __construct()
    {

    }

    public function authenticate()
    {
        $clientBuilder = new \Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientBuilder('https://training-api.dev.cloud.akeneo.com');

        $client = $clientBuilder->buildAuthenticatedByPassword(
            '9_1s327beliutcsc0s44woswcgwwwo8g8wsg400g8ok4cg0csoss',
            '20fra182299cc8w4c4k4o0gsc8g4k8ssk48gogw0wk8c444kgk',
            'leoconnection_5854',
            '3886f85c9'
        );

        return $client;
    }
}