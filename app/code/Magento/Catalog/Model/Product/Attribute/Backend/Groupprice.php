<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog product group price backend attribute model
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Attribute_Backend_Groupprice
    extends Magento_Catalog_Model_Product_Attribute_Backend_Groupprice_Abstract
{
    /**
     * Retrieve resource instance
     *
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Backend_Groupprice
     */
    protected function _getResource()
    {
        return Mage::getResourceSingleton('Magento_Catalog_Model_Resource_Product_Attribute_Backend_Groupprice');
    }

    /**
     * Error message when duplicates
     *
     * @return string
     */
    protected function _getDuplicateErrorMessage()
    {
        return __('We found a duplicate website group price customer group.');
    }
}
