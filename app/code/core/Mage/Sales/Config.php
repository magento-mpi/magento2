<?php

class Mage_Sales_Config
{
    public function getShippingOrigin()
    {
        $origin = array(
            'origin_country_id'=>223,
            'origin_region_id'=>1,
            'origin_zip'=>'90034',
        );
        return $origin;
    }
}