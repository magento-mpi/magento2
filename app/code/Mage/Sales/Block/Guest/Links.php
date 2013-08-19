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
 * Links block
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Guest_Links extends Mage_Page_Block_Template_Links_Block
{
    /**
     * Set link title, label and url
     */
    protected function _construct()
    {
        if (!Mage::getSingleton('Mage_Customer_Model_Session')->isLoggedIn()) {
            $this->_label       = __('Orders and Returns');
            $this->_title       = __('Orders and Returns');
            $this->_url         = $this->getUrl('sales/guest/form');

            parent::_construct();
        }
    }
}
