<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Links block
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Block_Guest_Links extends Magento_Page_Block_Template_Links_Block
{
    /**
     * Set link title, label and url
     */
    protected function _construct()
    {
        if (!Mage::getSingleton('Magento_Customer_Model_Session')->isLoggedIn()) {
            $this->_label       = __('Orders and Returns');
            $this->_title       = __('Orders and Returns');
            $this->_url         = $this->getUrl('sales/guest/form');

            parent::_construct();
        }
    }
}
