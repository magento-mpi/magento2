<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TargetRule\Controller\Adminhtml\Targetrule;

class Edit extends \Magento\TargetRule\Controller\Adminhtml\Targetrule
{
    /**
     * Edit action
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Related Products Rule'));

        /* @var $model \Magento\TargetRule\Model\Rule */
        $model = $this->_objectManager->create('Magento\TargetRule\Model\Rule');
        $ruleId = $this->getRequest()->getParam('id', null);

        if ($ruleId) {
            $model->load($ruleId);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This rule no longer exists.'));
                $this->_redirect('adminhtml/*');
                return;
            }
        }

        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getName() : __('New Related Products Rule')
        );

        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_coreRegistry->register('current_target_rule', $model);

        $this->_initAction();
        $this->_view->renderLayout();
    }
}
