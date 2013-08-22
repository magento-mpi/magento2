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
 * Interface of product configurational item option
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
interface Magento_Catalog_Model_Product_Configuration_Item_Option_Interface
{
    /**
     * Retrieve value associated with this option
     *
     * @return mixed
     */
    function getValue();
}
