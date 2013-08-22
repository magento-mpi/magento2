<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml Google Content Items Grids Container
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Block_Adminhtml_Items extends Magento_Adminhtml_Block_Widget_Grid_Container
{

    protected $_template = 'items.phtml';

    /**
     * Preparing layout
     *
     * @return Magento_GoogleShopping_Block_Adminhtml_Items
     */
    protected function _prepareLayout()
    {
        $this->addChild('item', 'Magento_GoogleShopping_Block_Adminhtml_Items_Item');
        $this->addChild('product', 'Magento_GoogleShopping_Block_Adminhtml_Items_Product');
        $this->addChild('store_switcher', 'Magento_GoogleShopping_Block_Adminhtml_Store_Switcher');

        return $this;
    }

    /**
     * Get HTML code for Store Switcher select
     *
     * @return string
     */
    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }

    /**
     * Get HTML code for CAPTCHA
     *
     * @return string
     */
    public function getCaptchaHtml()
    {
        return $this->getLayout()->createBlock('Magento_GoogleShopping_Block_Adminhtml_Captcha')
            ->setGcontentCaptchaToken($this->getGcontentCaptchaToken())
            ->setGcontentCaptchaUrl($this->getGcontentCaptchaUrl())
            ->toHtml();
    }

    /**
     * Get selecetd store
     *
     * @return Magento_Core_Model_Store
     */
    public function getStore()
    {
        return $this->_getData('store');
    }

    /**
     * Check whether synchronization process is running
     *
     * @return bool
     */
    public function isProcessRunning()
    {
        $flag = Mage::getModel('Magento_GoogleShopping_Model_Flag')->loadSelf();
        return $flag->isLocked();
    }

    /**
     * Build url for retrieving background process status
     *
     * @return string
     */
    public function getStatusUrl()
    {
        return $this->getUrl('*/*/status');
    }
}
