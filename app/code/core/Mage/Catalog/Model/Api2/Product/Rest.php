<?php

class Mage_Catalog_Model_Api2_Product_Rest extends Mage_Api2_Model_Resource_Instance
{
    /**
     * Fetch resource type
     * Resource type should correspond to api2.xml config nodes under "config/api2/resources/"
     *
     * @return string
     */
    public function getType()
    {
        return 'product';
    }
}
