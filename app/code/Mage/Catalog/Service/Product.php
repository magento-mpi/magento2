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
class Mage_Catalog_Service_Product
{
    const ERROR_INTERNAL_LOAD   = '01';
    const ERROR_INTERNAL_SAVE   = '02';
    const ERROR_INTERNAL_DELETE = '03';

    /**
     * @var Mage_Core_Service_Helper_Array
     */
    protected $_arrayHelper = null;

    /**
     * @param Mage_Core_Service_Helper_Array $helper
     */
    public function __constructor(Mage_Core_Service_Helper_Array $helper)
    {
        $this->_arrayHelper = $helper;
    }

    /**
     * Return resource object or resource object data.
     *
     * @param array $request
     * @throws Mage_Core_Service_Exception
     * @return array
     */
    public function item(array $request)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product');

        foreach ($request as $k => $v) {
            $product->setDataUsingMethod($k, $v);
        }

        $sku = $product->getSku();
        if ($sku) {
            $id = $product->getIdBySku($sku);
        } else {
            $id = $product->getEntityId();
        }

        if ($id) {
            // TODO: we need this trick as because of improper handling when target record doesn't exist
            $product->setEntityId(null);

            try {
                $product->load($id);
            } catch (Mage_Core_Service_Exception $e) {
                throw $e;
            } catch (Exception $e) {
                $message = Mage::helper('core')->__('An error occurred while loading the product.');
                throw new Mage_Core_Service_Exception($message, self::ERROR_INTERNAL_LOAD, $e);
            }
        }

        $result = $this->_arrayHelper->modelToArray($product, $request);

        return $result;
    }

    /**
     * Return collection of products.
     *
     * @param array $request
     * @throws Mage_Core_Service_Exception
     * @return array
     */
    public function items(array $request)
    {
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
            throw new Mage_Core_Service_Exception($message, self::ERROR_INTERNAL_LOAD);
        }

        $result = $this->_arrayHelper->collectionToArray($collection, $request);

        return $result;
    }

    /**
     * @param array $request
     * @throws Mage_Core_Service_Exception
     * @return array
     */
    public function create(array $request)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product');

        foreach ($request as $k => $v) {
            $product->setDataUsingMethod($k, $v);
        }

        try {
            $product->save();
        } catch (Mage_Core_Service_Exception $e) {
            throw $e;
        } catch (Exception $e) {
            $message = Mage::helper('core')->__('An error occurred while creating the product.');
            throw new Mage_Core_Service_Exception($message, self::ERROR_INTERNAL_SAVE);
        }

        $result = $this->_arrayHelper->modelToArray($product, $request);

        return $result;
    }

    /**
     * @param array $request
     * @throws Mage_Core_Service_Exception
     * @return array
     */
    public function update(array $request)
    {
        $data = $this->item($request);

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product');

        foreach ($data as $k => $v) {
            $product->setDataUsingMethod($k, $v);
        }

        foreach ($request as $k => $v) {
            $product->setDataUsingMethod($k, $v);
        }

        try {
            $product->save();
        } catch (Mage_Core_Service_Exception $e) {
            throw $e;
        } catch (Exception $e) {
            $message = Mage::helper('core')->__('An error occurred while updating the product.');
            throw new Mage_Core_Service_Exception($message, self::ERROR_INTERNAL_SAVE);
        }

        $result = $this->_arrayHelper->modelToArray($product, $request);

        return $result;
    }
}
