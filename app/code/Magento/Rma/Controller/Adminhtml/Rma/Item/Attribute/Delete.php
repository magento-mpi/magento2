<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml\Rma\Item\Attribute;

class Delete extends \Magento\Rma\Controller\Adminhtml\Rma\Item\Attribute
{
    /**
     * Delete attribute action
     *
     * @return void
     */
    public function execute()
    {
        $attributeId = $this->getRequest()->getParam('attribute_id');
        if ($attributeId) {
            $attributeObject = $this->_initAttribute()->load($attributeId)->setCanManageOptionLabels(true);
            if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId() ||
                !$attributeObject->getIsUserDefined()
            ) {
                $this->messageManager->addError(__('You cannot delete this attribute.'));
                $this->_redirect('adminhtml/*/');
                return;
            }
            try {
                $attributeObject->delete();

                $this->messageManager->addSuccess(__('You deleted the RMA attribute.'));
                $this->_redirect('adminhtml/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', array('attribute_id' => $attributeId, '_current' => true));
                return;
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong deleting the RMA item attribute.'));
                $this->_redirect('adminhtml/*/edit', array('attribute_id' => $attributeId, '_current' => true));
                return;
            }
        }

        $this->_redirect('adminhtml/*/');
        return;
    }
}
