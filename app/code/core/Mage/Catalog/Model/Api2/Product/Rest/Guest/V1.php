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
        $request = $this->getRequest();
        $response = $this->getResponse();

        $id = $request->getParam('id', null);

        $product = new Mage_Catalog_Model_Product;
        $product->load($id);

        //$product->setData($data);
        foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
            if (isset($data[$attribute->getAttributeCode()])) {
                $product->setData($attribute->getAttributeCode(), $data[$attribute->getAttributeCode()]);
            }
        }

        try {
            $product->save();
        } catch (Exception $e) {

            echo __FILE__;
            echo '<pre>';
            var_dump($e->getMessage());
            var_dump($e->getTraceAsString());
            echo '</pre>';
            exit;
        }



        /*$location = sprintf('/products/%s', $product->getId());
        $response->setHeader('Content-Location', $location);
        $response->setHeader('Location', $location);*/

        //$filtered = $filter->out($product->toArray());
        //return $filtered;

        //$response->setHttpResponseCode('200');
    }

    /**
     * Delete product
     */
    protected function _delete()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }
}
