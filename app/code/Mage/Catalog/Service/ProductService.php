<?php
/**
 * Catalog Product Entity Service.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *
 */
class Mage_Catalog_Service_ProductService extends Mage_Core_Service_Type_Abstract
{
    /**
     * Return resource object or resource object data.
     *
     * @param mixed $request
     * @param mixed $version [optional]
     * @return Mage_Catalog_Model_Product
     */
    public function item($request, $version = null)
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
     * @param mixed $version [optional]
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function items($request, $version = null)
    {
        $request = $this->prepareRequest(get_class($this), 'items', $request);

        /** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
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
}
