<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales orders grid row url generator
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Product_Grid_Row_UrlGenerator extends Mage_Backend_Model_Widget_Grid_Row_UrlGenerator
{
    /**
     * Generate row url
     * @param Varien_Object $item
     * @return bool|string
     */
    public function getUrl($item)
    {
        if (Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Reports::actions_view')) {
            return parent::getUrl($item);
        }
        return false;
    }
}
