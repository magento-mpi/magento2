<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Adminhtml\Index;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Message\Error;

class Validate extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * Customer validation
     *
     * @param \Magento\Framework\Object $response
     * @return CustomerInterface|null
     */
    protected function _validateCustomer($response)
    {
        $customer = null;
        $errors = null;

        try {
            /** @var CustomerInterface $customer */
            $customer = $this->_customerBuilder->create();

            $customerForm = $this->_formFactory->create(
                'customer',
                'adminhtml_customer',
                $this->_extensibleDataObjectConverter->toFlatArray($customer),
                true
            );
            $customerForm->setInvisibleIgnored(true);

            $data = $customerForm->extractData($this->getRequest(), 'account');

            if ($customer->getWebsiteId()) {
                unset($data['website_id']);
            }

            $customer = $this->_customerBuilder->populateWithArray($data)->create();
            $errors = $this->_customerAccountService->validateCustomerData($customer);
        } catch (\Magento\Framework\Model\Exception $exception) {
            /* @var $error Error */
            foreach ($exception->getMessages(\Magento\Framework\Message\MessageInterface::TYPE_ERROR) as $error) {
                $errors[] = $error->getText();
            }
        }

        if (!$errors->isValid()) {
            foreach ($errors->getMessages() as $error) {
                $this->messageManager->addError($error);
            }
            $response->setError(1);
        }

        return $customer;
    }

    /**
     * Customer address validation.
     *
     * @param \Magento\Framework\Object $response
     * @return void
     */
    protected function _validateCustomerAddress($response)
    {
        $addressesData = $this->getRequest()->getParam('address');
        if (is_array($addressesData)) {
            foreach (array_keys($addressesData) as $index) {
                if ($index == '_template_') {
                    continue;
                }

                $addressForm = $this->_formFactory->create('customer_address', 'adminhtml_customer_address');

                $requestScope = sprintf('address/%s', $index);
                $formData = $addressForm->extractData($this->getRequest(), $requestScope);

                $errors = $addressForm->validateData($formData);
                if ($errors !== true) {
                    foreach ($errors as $error) {
                        $this->messageManager->addError($error);
                    }
                    $response->setError(1);
                }
            }
        }
    }

    /**
     * AJAX customer validation action
     *
     * @return void
     */
    public function execute()
    {
        $response = new \Magento\Framework\Object();
        $response->setError(0);

        $customer = $this->_validateCustomer($response);
        if ($customer) {
            $this->_validateCustomerAddress($response);
        }

        if ($response->getError()) {
            $this->_view->getLayout()->initMessages();
            $response->setHtmlMessage($this->_view->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->representJson($response->toJson());
    }
}
