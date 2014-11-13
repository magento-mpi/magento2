<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml\Rma\Item\Attribute;

class Edit extends \Magento\Rma\Controller\Adminhtml\Rma\Item\Attribute
{
    /**
     * Edit attribute action
     *
     * @return void
     */
    public function execute()
    {
        /* @var $attributeObject \Magento\Rma\Model\Item\Attribute */
        $attributeId = $this->getRequest()->getParam('attribute_id');
        $attributeObject = $this->_initAttribute()->setEntityTypeId($this->_getEntityType()->getId());

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
        }

        $attributeData = $this->_getSession()->getAttributeData(true);
        if (!empty($attributeData)) {
            $attributeObject->setData($attributeData);
        }
        $attributeObject->setCanManageOptionLabels(true);
        $this->_coreRegistry->register('entity_attribute', $attributeObject);

        $label = $attributeObject->getId() ? __('Edit Return Item Attribute') : __('New Return Item Attribute');

        $this->_initAction()->_addBreadcrumb($label, $label);
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Returns Attributes'));
        $attributeId ? $this->_view->getPage()->getConfig()->getTitle()->prepend($attributeObject->getFrontendLabel())
            : $this->_view->getPage()->getConfig()->getTitle()->prepend(__('New Return Attribute'));
        $this->_view->renderLayout();
    }
}
