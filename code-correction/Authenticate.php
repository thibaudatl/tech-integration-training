<?php
//
//namespace ApiTraining;
//
//class Authenticate
//{
//    protected $clientID = '9_1s327beliutcsc0s44woswcgwwwo8g8wsg400g8ok4cg0csoss' ;
//    protected $secret = '20fra182299cc8w4c4k4o0gsc8g4k8ssk48gogw0wk8c444kgk' ;
//    protected $username = 'leoconnection_5854' ;
//    protected $password = '3886f85c9' ;
//
//    public function __construct()
//    {
//        $this->clientBuilder = new \Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientBuilder(
//            'https://training-api.dev.cloud.akeneo.com'
//        );
//    }
//
//    public function authenticate()
//    {
//        $client = $this->clientBuilder->buildAuthenticatedByPassword(
//            $this->clientID,
//            $this->secret,
//            $this->username,
//            $this->password
//        );
//
//        return $client;
//    }
//}