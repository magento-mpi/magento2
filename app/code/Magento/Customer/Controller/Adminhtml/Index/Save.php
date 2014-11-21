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

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    protected $_formFactory;

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

            $customerData = $this->_extractData(
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
     * Perform customer data filtration based on form code and form object
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param string $formCode The code of EAV form to take the list of attributes from
     * @param string $entityType entity type for the form
     * @param string[] $additionalAttributes The list of attribute codes to skip filtration for
     * @param string $scope scope of the request
     * @param \Magento\Customer\Model\Metadata\Form $metadataForm to use for extraction
     * @return array Filtered customer data
     */
    protected function _extractData(
        \Magento\Framework\App\RequestInterface $request,
        $formCode,
        $entityType,
        $additionalAttributes = array(),
        $scope = null,
        \Magento\Customer\Model\Metadata\Form $metadataForm = null
    ) {
        if (is_null($metadataForm)) {
            $metadataForm = $this->_objectManager->get('Magento\Customer\Model\Metadata\FormFactory')->create(
                $entityType,
                $formCode,
                array(),
                false,
                \Magento\Customer\Model\Metadata\Form::DONT_IGNORE_INVISIBLE
            );
        }
        $filteredData = $metadataForm->extractData($request, $scope);

        $object = $this->_objectFactory->create(['data' => $request->getPost()]);
        $requestData = $object->getData($scope);
        foreach ($additionalAttributes as $attributeCode) {
            $filteredData[$attributeCode] = isset($requestData[$attributeCode]) ? $requestData[$attributeCode] : false;
        }

        $formAttributes = $metadataForm->getAttributes();
        /** @var \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata $attribute */
        foreach ($formAttributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $frontendInput = $attribute->getFrontendInput();
            if ($frontendInput != 'boolean' && $filteredData[$attributeCode] === false) {
                unset($filteredData[$attributeCode]);
            }
        }

        return $filteredData;
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
        foreach ($addressIdList as $addressId) {
            $scope = sprintf('account/customer_address/%s', $addressId);
            $addressData = $this->_extractData(
                $this->getRequest(),
                'adminhtml_customer_address',
                \Magento\Customer\Api\AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
                array('default_billing', 'default_shipping'),
                $scope
            );

            if (is_numeric($addressId)) {
                $addressData['id'] = $addressId;
            }
            // Set default billing and shipping flags to customer
            if (!empty($addressData['default_billing']) && $addressData['default_billing'] === 'true') {
                $extractedCustomerData[Customer::DEFAULT_BILLING] = $addressId;
                $addressData['default_billing'] = true;
            } else {
                $addressData['default_billing'] = false;
            }
            if (!empty($addressData['default_shipping'])  && $addressData['default_shipping'] === 'true') {
                $extractedCustomerData[Customer::DEFAULT_SHIPPING] = $addressId;
                $addressData['default_shipping'] = true;
            } else {
                $addressData['default_shipping'] = false;
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
