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
namespace Magento\GoogleShopping\Block\Adminhtml;

class Items extends \Magento\Adminhtml\Block\Widget\Grid\Container
{

    protected $_template = 'items.phtml';

    /**
     * Preparing layout
     *
     * @return \Magento\GoogleShopping\Block\Adminhtml\Items
     */
    protected function _prepareLayout()
    {
        $this->addChild('item', 'Magento\GoogleShopping\Block\Adminhtml\Items\Item');
        $this->addChild('product', 'Magento\GoogleShopping\Block\Adminhtml\Items\Product');
        $this->addChild('store_switcher', 'Magento\GoogleShopping\Block\Adminhtml\Store\Switcher');

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
        return $this->getLayout()->createBlock('Magento\GoogleShopping\Block\Adminhtml\Captcha')
            ->setGcontentCaptchaToken($this->getGcontentCaptchaToken())
            ->setGcontentCaptchaUrl($this->getGcontentCaptchaUrl())
            ->toHtml();
    }

    /**
     * Get selecetd store
     *
     * @return \Magento\Core\Model\Store
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
        $flag = \Mage::getModel('Magento\GoogleShopping\Model\Flag')->loadSelf();
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
