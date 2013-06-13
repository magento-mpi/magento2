<?php
/**
 * TODO: Fake service for WSDL generation testing purposes.
 */
class Mage_Catalog_Service_Product
{
    public function item($request)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('Mage_Catalog_Model_Product')->load($request['entity_id']);
        return $product->getData();
    }
}
