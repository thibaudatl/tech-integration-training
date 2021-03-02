<?php


namespace export_products;


class AttributeTypesFormat
{

    public function getMetric(){
        $metric = [
            "locale" => "",
            "scope" => "",
            "data" => [
                "unit" => "",
                "value" =>""
            ]
        ];
        return $metric;
    }

    public function getPrice(){
        $price = [
            "locale" => "",
            "scope" => "",
            "data" => [
                "currency" => "",
                "value" => ""
            ]
        ];
        return $price;
    }

}