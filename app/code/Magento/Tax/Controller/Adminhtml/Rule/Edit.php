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

class Edit extends \Magento\Tax\Controller\Adminhtml\Rule
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Tax Rules'));

        $taxRuleId = $this->getRequest()->getParam('rule');
        $ruleModel = $this->_objectManager->create('Magento\Tax\Model\Calculation\Rule');
        if ($taxRuleId) {
            $ruleModel->load($taxRuleId);
            if (!$ruleModel->getId()) {
                $this->_objectManager->get('Magento\Backend\Model\Session')->unsRuleData();
                $this->messageManager->addError(__('This rule no longer exists.'));
                $this->_redirect('tax/*/');
                return;
            }
        }

        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getRuleData(true);
        if (!empty($data)) {
            $ruleModel->setData($data);
        }

        $this->_title->add($ruleModel->getId() ? sprintf("%s", $ruleModel->getCode()) : __('New Tax Rule'));

        $this->_coreRegistry->register('tax_rule', $ruleModel);

        $this->_initAction()->_addBreadcrumb(
            $taxRuleId ? __('Edit Rule') : __('New Rule'),
            $taxRuleId ? __('Edit Rule') : __('New Rule')
        );
        $this->_view->renderLayout();
    }
}
