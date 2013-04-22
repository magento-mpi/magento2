<?php
/**
 * API Product service.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *
 */
class Mage_Catalog_Service_ProductEntity extends Mage_Core_Service_Type_Abstract
{
    /**
     * Return resource object or resource object data.
     *
     * @param mixed $request
     * @return Mage_Catalog_Model_Product
     */
    public function item($request)
    {
        $request = $this->prepareRequest(get_class($this), 'item', $request);

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product');

        // `set` methods are creating troubles
        foreach ($request->getData() as $k => $v) {
            $product->setDataUsingMethod($k, $v);
        }

        $sku = $product->getSku();
        if ($sku) {
            $id = $product->getIdBySku($sku);
        } else {
            $id = $product->getId();
        }

        if ($id) {
            // TODO: we need this trick as because of improper handling when target record doesn't exist
            $product->setId(null);
            $product->load($id);
        }

        $this->prepareResponse(get_class($this), 'item', $product, $request);

        return $product;
    }

    /**
     * Return info about several products.
     *
     * @param mixed $request
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function items($request)
    {
        $request = $this->prepareRequest(get_class($this), 'items', $request);

        /** @var $collection Mage_Catalog_Model_Resource_Category_Collection */
        $collection = Mage::getResourceModel('Mage_Catalog_Model_Resource_Product_Collection');

        $helper = $this->_serviceManager->getServiceHelper('Mage_Core_Service_Helper_Filters');

        $helper->applyPaginationToCollection($collection, $request);

        $filters = $request->getFilters();
        if ($filters) {
            $helper->applyFiltersToCollection($collection, $filters);
        }

        // @todo or not TODO
        $collection->load();

        $this->prepareResponse(get_class($this), 'items', $collection, $request);

        return $collection;
    }

    /**
     * Return array which represents XSD "price" complex type.
     *
     * @todo should be a part of template rendering helper
     *
     * @param $amount
     * @param $currencyCode
     * @return array
     */
    private function _getPrice($amount, $currencyCode)
    {
        $price = array(
            'amount' => $amount,
            'currencyCode' => $currencyCode,
            'formattedPrice' => $this->_coreHelper->currency($amount, true, false),
        );

        return $price;
    }
}
