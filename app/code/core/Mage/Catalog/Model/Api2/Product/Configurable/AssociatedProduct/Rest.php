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
 * REST API for configurable product associated products resource
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Catalog_Model_Api2_Product_Configurable_AssociatedProduct_Rest
    extends Mage_Catalog_Model_Api2_Product_Rest
{
    /**
     * Associated products list is not available for guest and customer
     */
    protected function _retrieveCollection()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Associated products create is not available for guest and customer
     *
     * @param $data
     */
    protected function _create(array $data)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Associated products delete is not available for guest and customer
     */
    protected function _delete()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Retrieve resource data
     *
     * @return array
     */
    protected function _retrieve()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }

    /**
     * Update resource
     *
     * @param array $filteredData
     */
    protected function _update(array $filteredData)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }

    /**
     * Update a list of resources
     *
     * @param array $filteredData
     */
    protected function _multiUpdate(array $filteredData)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }

    /**
     * Delete a list of resources
     *
     * @param array $filteredData
     */
    protected function _multiDelete(array $filteredData)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }
}
