<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Adminhtml\Index;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Service\V1\Data\Customer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Service\V1\CustomerMetadataService as CustomerMetadata;

class Save extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * Reformat customer account data to be compatible with customer service interface
     *
     * @return array
     */
    protected function _extractCustomerData()
    {
        $customerData = array();
        if ($this->getRequest()->getPost('account')) {
            $serviceAttributes = array(
                Customer::DEFAULT_BILLING,
                Customer::DEFAULT_SHIPPING,
                'confirmation',
                'sendemail'
            );
            /** @var \Magento\Customer\Helper\Data $customerHelper */
            $customerHelper = $this->_objectManager->get('Magento\Customer\Helper\Data');
            $customerData = $customerHelper->extractCustomerData(
                $this->getRequest(),
                'adminhtml_customer',
                CustomerMetadata::ENTITY_TYPE_CUSTOMER,
                $serviceAttributes,
                'account'
            );
        }

        if (isset($customerData['disable_auto_group_change'])) {
            $customerData['disable_auto_group_change'] = (int)$customerData['disable_auto_group_change'];
        }

        return $customerData;
    }

    /**
     * Saves default_billing and default_shipping flags for customer address
     *
     * @param array $addressIdList
     * @param array $extractedCustomerData
     * @return array
     */
    protected function saveDefaultFlags(array $addressIdList, array & $extractedCustomerData)
    {
        $result = [];
        $extractedCustomerData[Customer::DEFAULT_BILLING] = null;
        $extractedCustomerData[Customer::DEFAULT_SHIPPING] = null;
        /** @var \Magento\Customer\Helper\Data $customerHelper */
        $customerHelper = $this->_objectManager->get('Magento\Customer\Helper\Data');
        foreach ($addressIdList as $addressId) {
            $scope = sprintf('account/customer_address/%s', $addressId);
            $addressData = $customerHelper->extractCustomerData(
                $this->getRequest(),
                'adminhtml_customer_address',
                \Magento\Customer\Api\AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
                array('is_default_billing', 'is_default_shipping'),
                $scope
            );

            if (is_numeric($addressId)) {
                $addressData['id'] = $addressId;
            }
            // Set default billing and shipping flags to customer
            if (isset($addressData['is_default_billing']) && $addressData['is_default_billing'] === 'true') {
                $extractedCustomerData[Customer::DEFAULT_BILLING] = $addressId;
            }
            if (isset($addressData['is_default_shipping']) && $addressData['is_default_shipping'] === 'true') {
                $extractedCustomerData[Customer::DEFAULT_SHIPPING] = $addressId;
            }

            $result[] = $addressData;
        }
        return $result;
    }

    /**
     * Reformat customer addresses data to be compatible with customer service interface
     *
     * @param array $extractedCustomerData
     * @return array
     */
    protected function _extractCustomerAddressData(array & $extractedCustomerData)
    {
        $customerData = $this->getRequest()->getPost('account');
        $addresses = isset($customerData['customer_address']) ? $customerData['customer_address'] : [];
        $result = array();
        if ($addresses) {
            if (isset($addresses['_template_'])) {
                unset($addresses['_template_']);
            }

            $addressIdList = array_keys($addresses);
            $result = $this->saveDefaultFlags($addressIdList, $extractedCustomerData);
        }

        return $result;
    }

    /**
     * Save customer action
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        $returnToEdit = false;
        $customerId = (int)$this->getRequest()->getParam('id');
        $originalRequestData = $this->getRequest()->getPost();
        if ($originalRequestData) {
            try {
                // optional fields might be set in request for future processing by observers in other modules
                $customerData = $this->_extractCustomerData();
                $addressesData = $this->_extractCustomerAddressData($customerData);
                $request = $this->getRequest();
                $isExistingCustomer = (bool)$customerId;
                $customerBuilder = $this->_customerBuilder;
                if ($isExistingCustomer) {
                    $savedCustomerData = $this->_customerAccountService->getCustomer($customerId);
                    $customerData = array_merge(
                        \Magento\Framework\Api\ExtensibleDataObjectConverter::toFlatArray($savedCustomerData),
                        $customerData
                    );
                }
                $customerBuilder->populateWithArray($customerData);
                $addresses = array();
                foreach ($addressesData as $addressData) {
                    $addresses[] = $this->_addressBuilder->populateWithArray($addressData)->create();
                }

                $this->_eventManager->dispatch(
                    'adminhtml_customer_prepare_save',
                    array('customer' => $customerBuilder, 'request' => $request)
                );
                $customer = $customerBuilder->create();

                // Save customer
                $customerDetails = $this->_customerDetailsBuilder->setCustomer(
                    $customer
                )->setAddresses($addresses)->create();
                if ($isExistingCustomer) {
                    $this->_customerAccountService->updateCustomer($customerId, $customerDetails);
                } else {
                    $customer = $this->_customerAccountService->createCustomer($customerDetails);
                    $customerId = $customer->getId();
                }

                $isSubscribed = false;
                if ($this->_authorization->isAllowed(null)) {
                    $isSubscribed = $this->getRequest()->getPost('subscription') !== null;
                }
                if ($isSubscribed) {
                    $this->_subscriberFactory->create()->subscribeCustomerById($customerId);
                } else {
                    $this->_subscriberFactory->create()->unsubscribeCustomerById($customerId);
                }

                // After save
                $this->_eventManager->dispatch(
                    'adminhtml_customer_save_after',
                    array('customer' => $customer, 'request' => $request)
                );
                $this->_getSession()->unsCustomerData();
                // Done Saving customer, finish save action
                $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customerId);
                $this->messageManager->addSuccess(__('You saved the customer.'));
                $returnToEdit = (bool)$this->getRequest()->getParam('back', false);
            } catch (\Magento\Framework\Validator\ValidatorException $exception) {
                $this->_addSessionErrorMessages($exception->getMessages());
                $this->_getSession()->setCustomerData($originalRequestData);
                $returnToEdit = true;
            } catch (\Magento\Framework\Model\Exception $exception) {
                $messages = $exception->getMessages(\Magento\Framework\Message\MessageInterface::TYPE_ERROR);
                if (!count($messages)) {
                    $messages = $exception->getMessage();
                }
                $this->_addSessionErrorMessages($messages);
                $this->_getSession()->setCustomerData($originalRequestData);
                $returnToEdit = true;
            } catch (LocalizedException $exception) {
                $this->_addSessionErrorMessages($exception->getMessage());
                $this->_getSession()->setCustomerData($originalRequestData);
                $returnToEdit = true;
            } catch (\Exception $exception) {
                $this->messageManager->addException($exception, __('An error occurred while saving the customer.'));
                $this->_getSession()->setCustomerData($originalRequestData);
                $returnToEdit = true;
            }
        }
        if ($returnToEdit) {
            if ($customerId) {
                $this->_redirect('customer/*/edit', array('id' => $customerId, '_current' => true));
            } else {
                $this->_redirect('customer/*/new', array('_current' => true));
            }
        } else {
            $this->_redirect('customer/index');
        }
    }
}
