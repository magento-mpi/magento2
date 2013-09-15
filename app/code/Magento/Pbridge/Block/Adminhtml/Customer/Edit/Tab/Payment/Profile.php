<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Customer Account Payment Profiles form block
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Pbridge\Block\Adminhtml\Customer\Edit\Tab\Payment;

class Profile
    extends \Magento\Pbridge\Block\Iframe\AbstractIframe
    implements \Magento\Adminhtml\Block\Widget\Tab\TabInterface
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
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Pbridge\Helper\Data $pbridgeData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Registry $registry,
        \Magento\Pbridge\Helper\Data $pbridgeData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($pbridgeData, $coreData, $context, $data);
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
        return \Mage::getStoreConfigFlag('payment/pbridge/profilestatus', $this->_getCurrentStore());
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
                'css_url'         => null,
                'customer_id'     => $this->getCustomerIdentifier(),
                'customer_name'   => $this->getCustomerName(),
                'customer_email'  => $this->getCustomerEmail()
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
     * @return \Magento\Core\Model\Store
     */
    protected function _getCurrentStore()
    {
        return $this->_getCurrentCustomer()->getStore();
    }
}
