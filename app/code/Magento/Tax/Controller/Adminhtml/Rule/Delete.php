<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Controller\Adminhtml\Rule;

use \Magento\Backend\App\Action;

class Delete extends \Magento\Tax\Controller\Adminhtml\Rule
{
    /**
     * @return void
     */
    public function execute()
    {
        $ruleId = (int)$this->getRequest()->getParam('rule');
        $ruleModel = $this->_objectManager->get('Magento\Tax\Model\Calculation\Rule')->load($ruleId);
        if (!$ruleModel->getId()) {
            $this->messageManager->addError(__('This rule no longer exists'));
            $this->_redirect('tax/*/');
            return;
        }

        try {
            $ruleModel->delete();

            $this->messageManager->addSuccess(__('The tax rule has been deleted.'));
            $this->_redirect('tax/*/');

            return;
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Something went wrong deleting this tax rule.'));
        }

        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
    }
}
