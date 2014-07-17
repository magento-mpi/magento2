<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Multishipping\Controller\Checkout\Address;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Service\V1\CustomerAddressServiceInterface;

class ShippingSaved extends \Magento\Multishipping\Controller\Checkout\Address
{
    /**
     * @var CustomerAddressServiceInterface
     */
    protected $_customerAddressService;

    /**
     * Initialize dependencies.
     *
     * @param Context $context
     * @param CustomerAddressServiceInterface $customerAddressService
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        CustomerAddressServiceInterface $customerAddressService
    ) {
        $this->_customerAddressService = $customerAddressService;
        parent::__construct($context);
    }

    /**
     * @return void
     */
    public function execute()
    {
        /**
         * if we create first address we need reset emd init checkout
         */
        $customerId = $this->_getCheckout()->getCustomer()->getId();
        if (count($this->_customerAddressService->getAddresses($customerId)) == 1) {
            $this->_getCheckout()->reset();
        }
        $this->_redirect('*/checkout/addresses');
    }
}
