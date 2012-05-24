<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Abstract class for product categories resource REST API
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Catalog_Model_Api2_Category_Product_Rest extends Mage_Catalog_Model_Api2_Category_Rest
{
    /**
     * Category product get is not available as it does not make sense
     */
    protected function _retrieve()
    {
        $this->_critical('Request does not match any route.', Mage_Api2_Model_Server::HTTP_NOT_FOUND);
    }

    /**
     * Category product list is not available for guest and customer
     */
    protected function _retrieveCollection()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Category product create is not available for guest and customer
     *
     * @param $data
     */
    protected function _create(array $data)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Category product update is not available for guest and customer
     *
     * @param $data
     */
    protected function _update(array $data)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Category product delete is not available for guest and customer
     */
    protected function _delete()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }
}
