<?php


abstract class Mage_Catalog_Model_Api2_Products_Rest extends Mage_Api2_Model_Resource_Collection
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
