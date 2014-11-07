<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Block\Adminhtml\Billing\Agreement\View\Tab;

use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template;

/**
 * Adminhtml billing agreement info tab
 */
class Info extends Template implements TabInterface
{
    /**
     * @var string
     */
    protected $_template = 'billing/agreement/view/tab/info.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param CustomerRepositoryInterface $customerRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CustomerRepositoryInterface $customerRepository,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_customerRepository = $customerRepository;
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
        $customer = $this->_customerRepository->getById($customerId);
        $this->setCustomerEmail($customer->getEmail());
        $this->setCustomerUrl($this->getUrl('customer/index/edit', array('id' => $customerId)));
        $this->setStatus($agreement->getStatusLabel());
        $this->setCreatedAt($this->formatDate($agreement->getCreatedAt(), 'short', true));
        $this->setUpdatedAt(
            $agreement->getUpdatedAt() ? $this->formatDate($agreement->getUpdatedAt(), 'short', true) : __('N/A')
        );

        return parent::_toHtml();
    }
}
