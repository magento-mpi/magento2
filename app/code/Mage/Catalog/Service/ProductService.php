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
     * @throws Mage_Core_Service_Exception
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

            try {
                $product->load($id);
            } catch (Mage_Core_Service_Exception $e) {
                throw $e;
            } catch (Exception $e) {
                $message = Mage::helper('core')->__('An error occurred while loading the product.');
                throw new Mage_Core_Service_Exception($message, Mage_Core_Service_Exception::HTTP_INTERNAL_ERROR);
            }
        }

        $this->prepareModel(get_class($this), 'item', $product, $request);

        return $product;
    }

    /**
     * Return collection of products.
     *
     * @param mixed $request
     * @param mixed $version [optional]
     * @throws Mage_Core_Service_Exception
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function items($request, $version = null)
    {
        $request = $this->prepareRequest(get_class($this), 'items', $request);

        /** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getResourceModel('Mage_Catalog_Model_Resource_Product_Collection');

        $helper = $this->_serviceManager->getServiceHelper('Mage_Core_Service_Helper_Filters');

        $helper->applyPaginationToCollection($collection, $request);

        $helper->applyFiltersToCollection($collection, $request);

        try {
            $collection->load();
        } catch (Mage_Core_Service_Exception $e) {
            throw $e;
        } catch (Exception $e) {
            $message = Mage::helper('core')->__('An error occurred while loading the product collection.');
            throw new Mage_Core_Service_Exception($message, Mage_Core_Service_Exception::HTTP_INTERNAL_ERROR);
        }

        $this->prepareCollection(get_class($this), 'items', $collection, $request);

        return $collection;
    }

    /**
     * @param mixed $request
     * @param string $version [optional]
     * @throws Mage_Core_Service_Exception
     */
    public function create($request, $version = null)
    {
        $request = $this->prepareRequest(get_class($this), 'create', $request);

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product');

        $product->setData($request->getData());

        try {
            $product->save();
        } catch (Mage_Core_Service_Exception $e) {
            throw $e;
        } catch (Exception $e) {
            $message = Mage::helper('core')->__('An error occurred while creating the product.');
            throw new Mage_Core_Service_Exception($message, Mage_Core_Service_Exception::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * @param mixed $request
     * @param string $version [optional]
     * @throws Mage_Core_Service_Exception
     */
    public function update($request, $version = null)
    {
        $request = $this->prepareRequest(get_class($this), 'update', $request);

        $product = $this->item($request);

        $product->addData($request->getData());

        try {
            $product->save();
        } catch (Mage_Core_Service_Exception $e) {
            throw $e;
        } catch (Exception $e) {
            $message = Mage::helper('core')->__('An error occurred while updating the product.');
            throw new Mage_Core_Service_Exception($message, Mage_Core_Service_Exception::HTTP_INTERNAL_ERROR);
        }
    }
}
