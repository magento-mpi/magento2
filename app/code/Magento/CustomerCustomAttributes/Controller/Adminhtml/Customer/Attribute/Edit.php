<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Attribute;

class Edit extends \Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Attribute
{
    /**
     * Edit attribute action
     *
     * @return void
     */
    public function execute()
    {
        /* @var $attributeObject \Magento\Customer\Model\Attribute */
        $attributeId = $this->getRequest()->getParam('attribute_id');
        $attributeObject = $this->_initAttribute()->setEntityTypeId($this->_getEntityType()->getId());

        $this->_title->add(__('Customer Attributes'));

        if ($attributeId) {
            $attributeObject->load($attributeId);
            if (!$attributeObject->getId()) {
                $this->messageManager->addError(__('The attribute no longer exists.'));
                $this->_redirect('adminhtml/*/');
                return;
            }
            if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId()) {
                $this->messageManager->addError(__('You cannot edit this attribute.'));
                $this->_redirect('adminhtml/*/');
                return;
            }

            $this->_title->add($attributeObject->getFrontendLabel());
        } else {
            $this->_title->add(__('New Customer Attribute'));
        }

        $attributeData = $this->_getSession()->getAttributeData(true);
        if (!empty($attributeData)) {
            $attributeObject->setData($attributeData);
        }
        $this->_coreRegistry->register('entity_attribute', $attributeObject);

        $label = $attributeObject->getId() ? __('Edit Customer Attribute') : __('New Customer Attribute');

        $this->_initAction()->_addBreadcrumb($label, $label);
        $this->_view->renderLayout();
    }
}
