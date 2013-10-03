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
     * Flag factory
     *
     * @var \Magento\GoogleShopping\Model\FlagFactory
     */
    protected $_flagFactory;

    /**
     * @param \Magento\GoogleShopping\Model\FlagFactory $flagFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\GoogleShopping\Model\FlagFactory $flagFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_flagFactory = $flagFactory;
        parent::__construct($coreData, $context, $data);
    }


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
        $flag = $this->_flagFactory->create()->loadSelf();
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
