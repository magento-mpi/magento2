<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Pbridge\Block\Adminhtml\Customer\Edit\Tab\Payment;

/**
 * Customer Account Payment Profiles form block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Profile extends \Magento\Pbridge\Block\Iframe\AbstractIframe implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Block template
     * @var string
     */
    protected $_template = 'customer/edit/tab/payment/profile.phtml';

    /**
     * Default iframe template
     *
     * @var string
     */
    protected $_iframeTemplate = 'Magento_Pbridge::iframe.phtml';

    /**
     * Default iframe height
     *
     * @var string
     */
    protected $_iframeHeight = '600';

    /**
     * Core registry
     *
     * @var \Magento\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Pbridge\Model\Session $pbridgeSession
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Pbridge\Helper\Data $pbridgeData
     * @param \Magento\App\Http\Context $httpContext
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Pbridge\Model\Session $pbridgeSession,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Pbridge\Helper\Data $pbridgeData,
        \Magento\App\Http\Context $httpContext,
        \Magento\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct(
            $context,
            $customerSession,
            $pbridgeSession,
            $regionFactory,
            $pbridgeData,
            $httpContext,
            $data
        );
    }

    /**
     * Getter for label
     * @return string
     */
    public function getTabLabel()
    {
        return __('Credit Cards');
    }

    /**
     * Getter for title
     * @return string
     */
    public function getTabTitle()
    {
        return __('Credit Cards');
    }

    /**
     * Whether tab can be shown
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->_coreRegistry->registry('current_customer')->getId() && $this->_isProfileEnable()) {
            return true;
        }
        return false;
    }

    /**
     * Whether tab is hidden
     * @return bool
     */
    public function isHidden()
    {
        if ($this->_coreRegistry->registry('current_customer')->getId() && $this->_isProfileEnable()) {
            return false;
        }
        return true;
    }

    /**
     * Check if payment profiles enabled
     * @return bool
     */
    protected function _isProfileEnable()
    {
        return $this->_storeConfig->isSetFlag('payment/pbridge/profilestatus', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_getCurrentStore());
    }

    /**
     * Precessor tab ID getter
     *
     * @return string
     */
    public function getAfter()
    {
        return 'reviews';
    }

    /**
     * Return iframe source URL
     * @return string
     */
    public function getSourceUrl()
    {
        $helper = $this->_pbridgeData;
        $helper->setStoreId($this->_getCurrentStore()->getId());
        return $helper->getPaymentProfileUrl(
            array(
                'billing_address' => $this->_getAddressInfo(),
                'css_url' => null,
                'customer_id' => $this->getCustomerIdentifier(),
                'customer_name' => $this->getCustomerName(),
                'customer_email' => $this->getCustomerEmail()
            )
        );
    }

    /**
     * Get current customer object
     *
     * @return null|\Magento\Customer\Model\Customer
     */
    protected function _getCurrentCustomer()
    {
        if ($this->_coreRegistry->registry('current_customer') instanceof \Magento\Customer\Model\Customer) {
            return $this->_coreRegistry->registry('current_customer');
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
        return $this->_getCurrentCustomer()->getStore();
    }
}
