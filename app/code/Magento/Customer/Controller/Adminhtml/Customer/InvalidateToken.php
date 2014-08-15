<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Controller\Adminhtml\Customer;
use Magento\Customer\Service\V1\Data\CustomerBuilder;
use Magento\Customer\Service\V1\CustomerAddressServiceInterface;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Customer\Service\V1\Data\AddressBuilder;
use Magento\Customer\Service\V1\Data\CustomerDetailsBuilder;

/**
 *  Class to invalidate tokens for customers
 */
class InvalidateToken extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * @var \Magento\Integration\Service\V1\TokenService
     */
    protected $_tokenService;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\Metadata\FormFactory $formFactory,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        CustomerBuilder $customerBuilder,
        CustomerDetailsBuilder $customerDetailsBuilder,
        AddressBuilder $addressBuilder,
        CustomerAddressServiceInterface $addressService,
        CustomerAccountServiceInterface $accountService,
        \Magento\Customer\Helper\View $viewHelper,
        \Magento\Customer\Helper\Data $helper,
        \Magento\Framework\Math\Random $random,
        \Magento\Integration\Service\V1\TokenService $tokenService
    ) {
        parent::__construct(
            $context,
            $coreRegistry,
            $fileFactory,
            $customerFactory,
            $addressFactory,
            $formFactory,
            $subscriberFactory,
            $customerBuilder,
            $customerDetailsBuilder,
            $addressBuilder,
            $addressService,
            $accountService,
            $viewHelper,
            $helper,
            $random
        );
        $this->_tokenService = $tokenService;
    }

    /**
     * Reset customer's tokens handler
     *
     * @return void
     */
    public function execute()
    {
        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            try {
                $this->_tokenService->revokeCustomerAccessToken($customerId);
                $this->messageManager->addSuccess(__('You have invalidated customer token.'));
                $this->_redirect('customer/*/edit', array('customer_id' => $customerId, '_current' => true));
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('*/*/');
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find a customer to invalidate token.'));
        $this->_redirect('adminhtml/*/');
    }
}