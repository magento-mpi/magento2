<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Address;

use Magento\Framework\Exception\InputException;

class FormPost extends \Magento\Customer\Controller\Address
{
    /**
     * Extract address from request
     *
     * @return \Magento\Customer\Service\V1\Data\Address
     */
    protected function _extractAddress()
    {
        $addressId = $this->getRequest()->getParam('id');
        $existingAddressData = array();
        if ($addressId) {
            $existingAddress = $this->_addressService->getAddress($addressId);
            if ($existingAddress->getId()) {
                $existingAddressData = \Magento\Customer\Service\V1\Data\AddressConverter::toFlatArray(
                    $existingAddress
                );
            }
        }

        /** @var \Magento\Customer\Model\Metadata\Form $addressForm */
        $addressForm = $this->_formFactory->create('customer_address', 'customer_address_edit', $existingAddressData);
        $addressData = $addressForm->extractData($this->getRequest());
        $attributeValues = $addressForm->compactData($addressData);
        $region = array('region_id' => $attributeValues['region_id'], 'region' => $attributeValues['region']);
        unset($attributeValues['region'], $attributeValues['region_id']);
        $attributeValues['region'] = $region;
        return $this->_addressBuilder->populateWithArray(
            array_merge($existingAddressData, $attributeValues)
        )->setDefaultBilling(
            $this->getRequest()->getParam('default_billing', false)
        )->setDefaultShipping(
            $this->getRequest()->getParam('default_shipping', false)
        )->create();
    }

    /**
     * Process address form save
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $this->_redirect('*/*/');
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->_getSession()->setAddressFormData($this->getRequest()->getPost());
            $this->getResponse()->setRedirect($this->_redirect->error($this->_buildUrl('*/*/edit')));
            return;
        }
        $customerId = $this->_getSession()->getCustomerId();
        try {
            $address = $this->_extractAddress();
            $this->_addressService->saveAddresses($customerId, array($address));
            $this->messageManager->addSuccess(__('The address has been saved.'));
            $url = $this->_buildUrl('*/*/index', array('_secure' => true));
            $this->getResponse()->setRedirect($this->_redirect->success($url));
            return;
        } catch (InputException $e) {
            $this->messageManager->addError($e->getMessage());
            foreach ($e->getErrors() as $error) {
                $this->messageManager->addError($error->getMessage());
            }
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Cannot save address.'));
        }

        $this->_getSession()->setAddressFormData($this->getRequest()->getPost());
        $url = $this->_buildUrl('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
        $this->getResponse()->setRedirect($this->_redirect->error($url));
    }
}
