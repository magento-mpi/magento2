<?php

class Mage_Catalog_Model_Product_Api2_Rest_Guest_V1 extends Mage_Api2_Model_Resource
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

        $product = new Mage_Catalog_Model_Product;
        $product->load($id);

        $filtered = $this->getFilter()->out($product->toArray());

        return $filtered;
    }

    /**
     * Dummy method
     *
     * @param array $data
     */
    protected function _create(array $data)
    {
        $this->fault('Resource does not support method.', 405);
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

        $filter = $this->getFilter();
        $data = $filter->in($data);

        $product = new Mage_Catalog_Model_Product;
        $product->load($id);

        //$product->setData($data);
        foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
            if (isset($data[$attribute->getAttributeCode()])) {
                $product->setData($attribute->getAttributeCode(), $data[$attribute->getAttributeCode()]);
            }
        }
        $product->save();


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
        $this->fault('Resource does not support method.', 405);
    }
}
