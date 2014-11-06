<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pbridge\Block\Adminhtml\Sales\Order\Create;

/**
 * Class AbstractCreate
 */
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
     * @var \Magento\Customer\Model\Converter
     */
    protected $_customerConverter;

    /**
     * Customer repository
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Pbridge\Model\Session $pbridgeSession
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Pbridge\Helper\Data $pbridgeData
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Backend\Model\Session\Quote $adminhtmlSessionQuote
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
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
        \Magento\Customer\Model\Converter $customerConverter,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        array $data = []
    ) {
        $this->_adminhtmlSessionQuote = $adminhtmlSessionQuote;
        $this->_backendUrl = $backendUrl;
        $this->_customerConverter = $customerConverter;
        $this->customerRepository = $customerRepository;
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
            ['store' => $this->getQuote()->getStoreId()]
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
     */
    protected function _getCurrentCustomer()
    {
        if ($this->_adminhtmlSessionQuote->hasCustomerId()) {
            return $this->customerRepository->getById($this->_adminhtmlSessionQuote->getCustomerId());
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
