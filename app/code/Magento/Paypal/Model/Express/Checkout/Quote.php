<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Model\Express\Checkout;

use Magento\Customer\Api\Data\AddressDataBuilder;
use Magento\Framework\Object\Copy as CopyObject;
use Magento\Customer\Api\Data\CustomerDataBuilder;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class Quote
 */
class Quote
{

    /**
     * @var AddressDataBuilder
     */
    protected $addressBuilder;

    /**
     * @var CustomerDataBuilder
     */
    protected $customerBuilder;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var CopyObject
     */
    protected $copyObject;

    /**
     * @param AddressDataBuilder $addressBuilder
     * @param CustomerDataBuilder $customerBuilder
     * @param CustomerRepositoryInterface $customerRepository
     * @param CopyObject $copyObject
     * @param CustomerSession $customerSession
     */
    public function __construct(
        AddressDataBuilder $addressBuilder,
        CustomerDataBuilder $customerBuilder,
        CustomerRepositoryInterface $customerRepository,
        CopyObject $copyObject,
        CustomerSession $customerSession
    ) {
        $this->addressBuilder = $addressBuilder;
        $this->customerBuilder = $customerBuilder;
        $this->customerRepository = $customerRepository;
        $this->copyObject = $copyObject;
        $this->_customerSession =
            isset($params['session']) && $params['session'] instanceof \Magento\Customer\Model\Session ?
                $params['session'] :
                $customerSession;
    }

    /**
     * @param \Magento\Sales\Model\Quote $quote
     * @return void
     */
    public function prepareQuoteForNewCustomer(\Magento\Sales\Model\Quote $quote)
    {
        $billing    = $quote->getBillingAddress();
        $shipping   = $quote->isVirtual() ? null : $quote->getShippingAddress();
        $billing->setDefaultBilling(true);
        if ($shipping && !$shipping->getSameAsBilling()) {
            $shipping->setDefaultShipping(true);
            $shipping->setCustomerAddressData(
                $this->addressBuilder->populateWithArray($shipping->getData())->create()
            );
        } elseif ($shipping) {
            $billing->setDefaultShipping(true);
        }
        $billing->setCustomerAddressData(
            $this->addressBuilder->populateWithArray($shipping->getData())->create()
        );
        foreach (['customer_dob', 'customer_taxvat', 'customer_gender'] as $attribute) {
            if ($quote->getData($attribute) && !$billing->getData($attribute)) {
                $billing->setData($attribute, $quote->getData($attribute));
            }
        }
        $this->customerBuilder->populateWithArray(
            $this->copyObject->getDataFromFieldset(
                'checkout_onepage_billing',
                'to_customer',
                $billing
            )
        );
        $this->customerBuilder->setEmail($quote->getCustomerEmail());
        $this->customerBuilder->setPrefix($quote->getCustomerPrefix());
        $this->customerBuilder->setFirstname($quote->getCustomerFirstname());
        $this->customerBuilder->setMiddlename($quote->getCustomerMiddlename());
        $this->customerBuilder->setLastname($quote->getCustomerLastname());
        $this->customerBuilder->setSuffix($quote->getCustomerSuffix());
        $quote->setCustomerData($this->customerBuilder->create());
        $quote->addCustomerAddressData($billing->getCustomerAddressData());
        if ($shipping->hasCustomerAddressData()) {
            $quote->addCustomerAddressData($shipping->getCustomerAddressData());
        }
    }

    /**
     * @param \Magento\Sales\Model\Quote $quote
     * @return void
     */
    public function prepareRegisteredCustomerQuote(\Magento\Sales\Model\Quote $quote)
    {
        $billing    = $quote->getBillingAddress();
        $shipping   = $quote->isVirtual() ? null : $quote->getShippingAddress();
        $customer = $this->customerRepository->getById($this->customerSession->getCustomerId());
        if (!$billing->getCustomerId() || $billing->getSaveInAddressBook()) {
            $billing->setCustomerAddressData(
                $this->addressBuilder->populateWithArray($billing->getData())->create()
            );
        }
        if ($shipping && !$shipping->getSameAsBilling()
            && (!$shipping->getCustomerId() || $shipping->getSaveInAddressBook())
        ) {
            $shipping->setCustomerAddressData(
                $this->addressBuilder->populateWithArray($shipping->getData())->create()
            );
        }
        $isBillingAddressDefaultBilling = !!$customer->getDefaultBilling();
        $isBillingAddressDefaultShipping = false;
        if ($shipping && !$customer->getDefaultShipping()) {
            $shipping->setDefaultBilling(false);
            $shipping->setDefaultShipping(true);
            $quote->addCustomerAddressData($this->addressBuilder->populateWithArray($shipping->getData())->create());
        } else if (!$customer->getDefaultShipping()) {
            $isBillingAddressDefaultShipping = true;
        }
        if ($billing) {
            $billing->setDefaultBilling($isBillingAddressDefaultBilling);
            $billing->setDefaultShipping($isBillingAddressDefaultShipping);
            $quote->addCustomerAddressData($this->addressBuilder->populateWithArray($billing->getData())->create());
        }
        $quote->setCustomerData($customer);
    }
}
