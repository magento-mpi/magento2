<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Abstract API2 class for order address
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Sales_Model_Api2_Order_Address_Rest extends Mage_Sales_Model_Api2_Order_Address
{
    /**#@+
     * Parameters in request used in model (usually specified in route mask)
     */
    const PARAM_ORDER_ID     = 'order_id';
    const PARAM_ADDRESS_TYPE = 'address_type';
    /**#@-*/

    /**
     * Retrieve order address
     *
     * @return array
     */
    protected function _retrieve()
    {
        /** @var $address Mage_Sales_Model_Order_Address */
        $address = $this->_getCollectionForRetrieve()
            ->addAttributeToFilter('address_type', $this->getRequest()->getParam(self::PARAM_ADDRESS_TYPE))
            ->getFirstItem();
        if (!$address->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $address->getData();
    }

    /**
     * Retrieve order addresses
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $collection = $this->_getCollectionForRetrieve();

        $this->_applyCollectionModifiers($collection);
        $data = $collection->load()->toArray();

        if (0 == count($data['items'])) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        return $data['items'];
    }

    /**
     * Retrieve collection instances
     *
     * @return Mage_Sales_Model_Resource_Order_Address_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        /* @var $collection Mage_Sales_Model_Resource_Order_Address_Collection */
        $collection = Mage::getResourceModel('Mage_Sales_Model_Resource_Order_Address_Collection');
        $collection->addAttributeToFilter('parent_id', $this->getRequest()->getParam(self::PARAM_ORDER_ID));

        return $collection;
    }
}
