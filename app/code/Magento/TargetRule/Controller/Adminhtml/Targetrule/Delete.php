<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\TargetRule\Controller\Adminhtml\Targetrule;

class Delete extends \Magento\TargetRule\Controller\Adminhtml\Targetrule
{
    /**
     * Delete target rule
     *
     * @return void
     */
    public function execute()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = $this->_objectManager->create('Magento\TargetRule\Model\Rule');
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('You deleted the rule.'));
                $this->_redirect('adminhtml/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addError(__("We can't find a page to delete."));
        $this->_redirect('adminhtml/*/');
    }
}
