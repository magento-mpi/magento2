<?php

class Mage_Catalog_Model_Api2_Product_Rest_Guest_V1 extends Mage_Catalog_Model_Api2_Product_Rest
{
    /**
     * Get product
     *
     * @return array
     */
    protected function _retrieve()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id', null);

        //$this->getResponse()->setBody($id)->sendResponse(); exit;

        if (!preg_match('/^([0-9]+)$/', $id)) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        $product = Mage::getModel('catalog/product');
        $product->load($id);

        if (!$product->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        return $product->toArray();
    }

    /**
     * Update product
     *
     * @param array $data
     */
    protected function _update(array $data)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Delete product
     */
    protected function _delete()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }
}
