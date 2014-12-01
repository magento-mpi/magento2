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
        $taxRuleId = $this->getRequest()->getParam('rule');
        $this->_coreRegistry->register('tax_rule_id', $taxRuleId);
        /** @var \Magento\Backend\Model\Session $backendSession */
        $backendSession = $this->_objectManager->get('Magento\Backend\Model\Session');
        if ($taxRuleId) {
            try {
                $taxRule = $this->ruleService->get($taxRuleId);
                $pageTitle = sprintf("%s", $taxRule->getCode());
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $backendSession->unsRuleData();
                $this->messageManager->addError(__('This rule no longer exists.'));
                $this->_redirect('tax/*/');
                return;
            }
        } else {
            $pageTitle = __('New Tax Rule');
        }
        $data = $backendSession->getRuleData(true);
        if (!empty($data)) {
            $this->_coreRegistry->register('tax_rule_form_data', $data);
        }
        $breadcrumb = $taxRuleId ? __('Edit Rule') : __('New Rule');
        $this->_initAction()->_addBreadcrumb($breadcrumb, $breadcrumb);
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Tax Rules'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend($pageTitle);
        $this->_view->renderLayout();
    }
}
