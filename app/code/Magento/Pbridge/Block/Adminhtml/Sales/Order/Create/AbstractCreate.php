<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pbridge\Block\Adminhtml\Sales\Order\Create;

class AbstractCreate
    extends \Magento\Pbridge\Block\Payment\Form\AbstractForm
{
    /**
     * Payment code
     *
     * @var string
     */
    protected $_code;

    /**
     * Adminhtml template for payment form block
     *
     * @var string
     */
    protected $_template = 'Magento_Pbridge::sales/order/create/pbridge.phtml';

    /**
     * Adminhtml Iframe block type
     *
     * @var string
     */
    protected $_iframeBlockType = 'Magento\Backend\Block\Template';

    /**
     * Adminhtml iframe template
     *
     * @var string
     */
    protected $_iframeTemplate = 'Magento_Pbridge::iframe.phtml';

    /**
     * Backend url
     *
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * Adminhtml session quote
     *
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $_adminhtmlSessionQuote;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Pbridge\Model\Session $pbridgeSession
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Pbridge\Helper\Data $pbridgeData
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Backend\Model\Session\Quote $adminhtmlSessionQuote
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Pbridge\Model\Session $pbridgeSession,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Pbridge\Helper\Data $pbridgeData,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Backend\Model\Session\Quote $adminhtmlSessionQuote,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        array $data = array()
    ) {
        $this->_adminhtmlSessionQuote = $adminhtmlSessionQuote;
        $this->_backendUrl = $backendUrl;
        parent::__construct(
            $context,
            $customerSession,
            $pbridgeSession,
            $regionFactory,
            $pbridgeData,
            $checkoutSession,
            $data
        );
    }

    /**
     * Return redirect url for Payment Bridge application
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->_backendUrl->getUrl('adminhtml/pbridge/result',
            array('store' => $this->getQuote()->getStoreId())
        );
    }

    /**
     * Getter
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return $this->_adminhtmlSessionQuote->getQuote();
    }

    /**
     * Generate and return variation code
     *
     * @return string
     */
    protected function _getVariation()
    {
        return $this->_storeConfig->getValue('payment/pbridge/merchantcode', 'default')
            . '_' . $this->getQuote()->getStore()->getWebsite()->getCode();
    }

    /**
     * Disable external CSS in admin order creation
     * @return null
     */
    public function getCssUrl()
    {
        return null;
    }

    /**
     * Get current customer object
     *
     * @return null|\Magento\Customer\Model\Customer
     */
    protected function _getCurrentCustomer()
    {
        if ($this->_adminhtmlSessionQuote->getCustomer() instanceof \Magento\Customer\Model\Customer) {
            return $this->_adminhtmlSessionQuote->getCustomer();
        }

        return null;
    }

    /**
     * Return store for current context
     *
     * @return \Magento\Store\Model\Store
     */
    protected function _getCurrentStore()
    {
        return $this->getQuote()->getStore();
    }
}
