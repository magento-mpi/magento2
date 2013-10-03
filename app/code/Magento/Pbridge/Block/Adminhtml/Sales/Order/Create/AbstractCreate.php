<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Paypal Direct payment block
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Pbridge\Block\Adminhtml\Sales\Order\Create;

class AbstractCreate
    extends \Magento\Pbridge\Block\Payment\Form\AbstractForm
{
    /**
     * Paypal payment code
     *
     * @var string
     */
    protected $_code = \Magento\Paypal\Model\Config::METHOD_WPP_DIRECT;

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
    protected $_iframeBlockType = 'Magento\Adminhtml\Block\Template';

    /**
     * Adminhtml iframe template
     *
     * @var string
     */
    protected $_iframeTemplate = 'Magento_Pbridge::iframe.phtml';

    /**
     * Backend url
     *
     * @var \Magento\Backend\Model\Url
     */
    protected $_backendUrl;

    /**
     * Adminhtml session quote
     *
     * @var \Magento\Adminhtml\Model\Session\Quote
     */
    protected $_adminhtmlSessionQuote;

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_config;

    /**
     * Construct
     *
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Pbridge\Model\Session $pbridgeSession
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Pbridge\Helper\Data $pbridgeData
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Adminhtml\Model\Session\Quote $adminhtmlSessionQuote
     * @param \Magento\Backend\Model\Url $backendUrl
     * @param \Magento\Core\Model\Config $config
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Pbridge\Model\Session $pbridgeSession,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Pbridge\Helper\Data $pbridgeData,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Adminhtml\Model\Session\Quote $adminhtmlSessionQuote,
        \Magento\Backend\Model\Url $backendUrl,
        \Magento\Core\Model\Config $config,
        array $data = array()
    ) {
        $this->_adminhtmlSessionQuote = $adminhtmlSessionQuote;
        $this->_backendUrl = $backendUrl;
        $this->_config = $config;
        parent::__construct($coreData, $context, $customerSession, $pbridgeSession, $regionFactory, $storeManager,
            $pbridgeData, $checkoutSession, $data);
    }

    /**
     * Return redirect url for Payment Bridge application
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->_backendUrl->getUrl('*/pbridge/result',
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
        return $this->_config->getValue('payment/pbridge/merchantcode', 'default')
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
     * @return \Magento\Core\Model\Store
     */
    protected function _getCurrentStore()
    {
        return $this->getQuote()->getStore();
    }
}
