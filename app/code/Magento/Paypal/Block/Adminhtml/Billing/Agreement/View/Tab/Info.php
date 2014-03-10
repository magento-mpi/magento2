<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Block\Adminhtml\Billing\Agreement\View\Tab;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;

/**
 * Adminhtml billing agreement info tab
 */
class Info extends \Magento\Backend\Block\Template
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var string
     */
    protected $_template = 'billing/agreement/view/tab/info.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Customer service
     *
     * @var CustomerAccountServiceInterface
     */
    protected $_customerAccountService;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param CustomerAccountServiceInterface $customerAccountService
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        CustomerAccountServiceInterface $customerAccountService,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_customerAccountService = $customerAccountService;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('General Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('General Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Retrieve billing agreement model
     *
     * @return \Magento\Paypal\Model\Billing\Agreement
     */
    protected function _getBillingAgreement()
    {
        return $this->_coreRegistry->registry('current_billing_agreement');
    }

    /**
     * Set data to block
     *
     * @return string
     */
    protected function _toHtml()
    {
        $agreement = $this->_getBillingAgreement();
        $this->setReferenceId($agreement->getReferenceId());
        $customerId = $agreement->getCustomerId();
        $customer = $this->_customerAccountService->getCustomer($customerId);

        $this->setCustomerEmail($customer->getEmail());
        $this->setCustomerUrl(
            $this->getUrl('customer/index/edit', array('id' => $customerId))
        );
        $this->setStatus($agreement->getStatusLabel());
        $this->setCreatedAt(
            $this->formatDate($agreement->getCreatedAt(), 'short', true)
        );
        $this->setUpdatedAt(
            ($agreement->getUpdatedAt())
                ? $this->formatDate($agreement->getUpdatedAt(), 'short', true)
                : __('N/A')
        );

        return parent::_toHtml();
    }
}
