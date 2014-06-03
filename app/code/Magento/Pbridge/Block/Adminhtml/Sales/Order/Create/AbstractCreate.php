<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pbridge\Block\Adminhtml\Sales\Order\Create;

class AbstractCreate extends \Magento\Pbridge\Block\Payment\Form\AbstractForm
{
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
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $_customerService;

    /**
     * @var \Magento\Customer\Model\Converter
     */
    protected $_customerConverter;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Pbridge\Model\Session $pbridgeSession
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Pbridge\Helper\Data $pbridgeData
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Backend\Model\Session\Quote $adminhtmlSessionQuote
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerService
     * @param \Magento\Customer\Model\Converter $customerConverter
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Pbridge\Model\Session $pbridgeSession,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Pbridge\Helper\Data $pbridgeData,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Backend\Model\Session\Quote $adminhtmlSessionQuote,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerService,
        \Magento\Customer\Model\Converter $customerConverter,
        array $data = array()
    ) {
        $this->_adminhtmlSessionQuote = $adminhtmlSessionQuote;
        $this->_backendUrl = $backendUrl;
        $this->_customerService = $customerService;
        $this->_customerConverter = $customerConverter;
        parent::__construct(
            $context,
            $customerSession,
            $checkoutSession,
            $pbridgeSession,
            $regionFactory,
            $pbridgeData,
            $httpContext,
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
        return $this->_backendUrl->getUrl(
            'adminhtml/pbridge/result',
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
        return $this->_scopeConfig->getValue(
            'payment/pbridge/merchantcode',
            'default'
        ) . '_' . $this->getQuote()->getStore()->getWebsite()->getCode();
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
     * @return \Magento\Customer\Model\Customer|null
     * @deprecated Use _getCurrentCustomerData() instead
     */
    protected function _getCurrentCustomer()
    {
        /**
         * TODO: This method should be removed when all external dependencies are refactored
         * and converter usage should be eliminated
         */
        if ($this->_adminhtmlSessionQuote->hasCustomerId()) {
            return $this->_customerConverter->createCustomerModel($this->_getCurrentCustomerData());
        }
        return null;
    }

    /**
     * Get current customer data object.
     *
     * @return \Magento\Customer\Service\V1\Data\Customer|null
     */
    protected function _getCurrentCustomerData()
    {
        if ($this->_adminhtmlSessionQuote->hasCustomerId()) {
            return $this->_customerService->getCustomer($this->_adminhtmlSessionQuote->getCustomerId());
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
