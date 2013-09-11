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
namespace Magento\Sales\Block\Guest;

class Links extends \Magento\Page\Block\Template\Links\Block
{
    /**
     * Set link title, label and url
     */
    protected function _construct()
    {
        if (!\Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()) {
            $this->_label       = __('Orders and Returns');
            $this->_title       = __('Orders and Returns');
            $this->_url         = $this->getUrl('sales/guest/form');

            parent::_construct();
        }
    }
}
