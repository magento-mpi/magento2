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
 * Catalog Compare Products Abstract Block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Catalog_Block_Product_Compare_Abstract extends Magento_Catalog_Block_Product_Abstract
{
    /**
     * Retrieve Product Compare Helper
     *
     * @return Magento_Catalog_Helper_Product_Compare
     */
    protected function _getHelper()
    {
        return Mage::helper('Magento_Catalog_Helper_Product_Compare');
    }

    /**
     * Retrieve Remove Item from Compare List URL
     *
     * @param Magento_Catalog_Model_Product $item
     * @return string
     */
    public function getRemoveUrl($item)
    {
        return $this->_getHelper()->getRemoveUrl($item);
    }
}
