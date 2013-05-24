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
class Mage_Catalog_Service_Product extends Mage_Core_Service_Type_Abstract
{
    const ERROR_INTERNAL_LOAD   = '01';
    const ERROR_INTERNAL_SAVE   = '02';
    const ERROR_INTERNAL_DELETE = '03';

    /**
     * @var $_serviceID string
     */
    protected $_serviceID = 'Mage_Catalog_Service_Product';

    /**
     * @var $_serviceVersion string
     */
    protected $_serviceVersion = '1';

    /**
     * Return resource object or resource object data.
     *
     * @param mixed $request
     * @throws Mage_Core_Service_Exception
     * @return Varien_Object | array
     */
    public function item($request)
    {
        $request = $this->_prepareRequest('item', $request);

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

        $result = $this->_prepareModel('item', $product, $request);

        return $result;
    }

    /**
     * Return collection of products.
     *
     * @param mixed $request
     * @throws Mage_Core_Service_Exception
     * @return Magento_Data_Collection | array
     */
    public function items($request)
    {
        $request = $this->_prepareRequest('items', $request);

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

        $result = $this->_prepareCollection('items', $collection, $request);

        return $result;
    }

    /**
     * @param mixed $request
     * @throws Mage_Core_Service_Exception
     * @return Varien_Object | array
     */
    public function create($request)
    {
        $request = $this->_prepareRequest('create', $request);

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product');

        $product->setData($request->getData());

        try {
            $product->save();
        } catch (Mage_Core_Service_Exception $e) {
            throw $e;
        } catch (Exception $e) {
            $message = Mage::helper('core')->__('An error occurred while creating the product.');
            throw new Mage_Core_Service_Exception($message, self::ERROR_INTERNAL_SAVE);
        }

        $result = $this->prepareModel('item', $product, $request);

        return $result;
    }

    /**
     * @param mixed $request
     * @throws Mage_Core_Service_Exception
     * @return Varien_Object | array
     */
    public function update($request)
    {
        $request = $this->_prepareRequest('update', $request);
        $data    = $this->item($request);

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->setData($data);
        $product->addData($request->getData());

        try {
            $product->save();
        } catch (Mage_Core_Service_Exception $e) {
            throw $e;
        } catch (Exception $e) {
            $message = Mage::helper('core')->__('An error occurred while updating the product.');
            throw new Mage_Core_Service_Exception($message, self::ERROR_INTERNAL_SAVE);
        }

        $result = $this->prepareModel(get_class($this), 'item', $product, $request);

        return $result;
    }
}
