<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Adminhtml\Order\Create;

/**
 * Create order form header
 */
class Header extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    /** @var \Magento\Customer\Service\V1\CustomerServiceInterface */
    protected $_customerService;

    /** @var \Magento\Customer\Helper\View */
    protected $_customerViewHelper;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Magento\Customer\Service\V1\CustomerServiceInterface $customerService
     * @param \Magento\Customer\Helper\View $customerViewHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Customer\Service\V1\CustomerServiceInterface $customerService,
        \Magento\Customer\Helper\View $customerViewHelper,
        array $data = array()
    ) {
        $this->_customerService = $customerService;
        $this->_customerViewHelper = $customerViewHelper;
        parent::__construct($context, $sessionQuote, $orderCreate, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if ($this->_getSession()->getOrder()->getId()) {
            return __('Edit Order #%1', $this->_getSession()->getOrder()->getIncrementId());
        }
        $out = $this->_getCreateOrderTitle();
        return $this->escapeHtml($out);
    }

    /**
     * Generate title for new order creation page.
     *
     * @return string
     */
    protected function _getCreateOrderTitle()
    {
        $customerId = $this->getCustomerId();
        $storeId = $this->getStoreId();
        $out = '';
        if ($customerId && $storeId) {
            $out .= __(
                'Create New Order for %1 in %2',
                $this->_getCustomerName($customerId),
                $this->getStore()->getName()
            );
            return $out;
        } elseif (!$customerId && $storeId) {
            $out .= __('Create New Order for New Customer in %1', $this->getStore()->getName());
            return $out;
        } elseif ($customerId && !$storeId) {
            $out .= __('Create New Order for %1', $this->_getCustomerName($customerId));
            return $out;
        } elseif (!$customerId && !$storeId) {
            $out .= __('Create New Order for New Customer');
            return $out;
        }
        return $out;
    }

    /**
     * Get customer name by his ID.
     *
     * @param int $customerId
     * @return string
     */
    protected function _getCustomerName($customerId)
    {
        $customerData = $this->_customerService->getCustomer($customerId);
        return $this->_customerViewHelper->getCustomerName($customerData);
    }
}
