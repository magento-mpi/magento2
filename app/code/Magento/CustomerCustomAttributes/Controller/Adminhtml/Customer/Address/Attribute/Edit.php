<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Address\Attribute;

class Edit extends \Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Address\Attribute
{
    /**
     * Edit attribute action
     *
     * @return void
     */
    public function execute()
    {
        $attributeId = $this->getRequest()->getParam('attribute_id');
        /* @var $attributeObject \Magento\Customer\Model\Attribute */
        $attributeObject = $this->_initAttribute()->setEntityTypeId($this->_getEntityType()->getId());

        $this->_title->add(__('Customer Address Attributes'));

        if ($attributeId) {
            $attributeObject->load($attributeId);
            if (!$attributeObject->getId()) {
                $this->messageManager->addError(__('Attribute is no longer exists.'));
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
            $this->_title->add(__('New Customer Address Attribute'));
        }

        // restore attribute data
        $attributeData = $this->_getSession()->getAttributeData(true);
        if (!empty($attributeData)) {
            $attributeObject->setData($attributeData);
        }

        // register attribute object
        $this->_coreRegistry->register('entity_attribute', $attributeObject);

        $label = $attributeObject->getId() ? __(
            'Edit Customer Address Attribute'
        ) : __(
            'New Customer Address Attribute'
        );

        $this->_initAction()->_addBreadcrumb($label, $label);
        $this->_view->renderLayout();
    }
}
