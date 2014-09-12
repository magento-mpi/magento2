<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TargetRule\Controller\Adminhtml\Targetrule;

class Save extends \Magento\TargetRule\Controller\Adminhtml\Targetrule
{
    /**
     * Save action
     *
     * @return void
     */
    public function execute()
    {
        $redirectPath = '*/*/';
        $redirectParams = array();

        $data = $this->getRequest()->getPost();

        if ($this->getRequest()->isPost() && $data) {
            /* @var $model \Magento\TargetRule\Model\Rule */
            $model = $this->_objectManager->create('Magento\TargetRule\Model\Rule');
            $redirectBack = $this->getRequest()->getParam('back', false);
            $hasError = false;

            try {
                $inputFilter = new \Zend_Filter_Input(
                    array('from_date' => $this->_dateFilter, 'to_date' => $this->_dateFilter),
                    array(),
                    $data
                );
                $data = $inputFilter->getUnescaped();
                $ruleId = $this->getRequest()->getParam('rule_id');
                if ($ruleId) {
                    $model->load($ruleId);
                    if ($ruleId != $model->getId()) {
                        throw new \Magento\Framework\Model\Exception(__('Please specify a correct rule.'));
                    }
                }

                $validateResult = $model->validateData(new \Magento\Framework\Object($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->messageManager->addError($errorMessage);
                    }
                    $this->_getSession()->setFormData($data);

                    $this->_redirect('adminhtml/*/edit', array('id' => $model->getId()));
                    return;
                }

                $data['conditions'] = $data['rule']['conditions'];
                $data['actions'] = $data['rule']['actions'];
                unset($data['rule']);

                $model->loadPost($data);
                $model->save();

                $this->messageManager->addSuccess(__('You saved the rule.'));

                if ($redirectBack) {
                    $this->_redirect('adminhtml/*/edit', array('id' => $model->getId(), '_current' => true));
                    return;
                }
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $hasError = true;
            } catch (\Zend_Date_Exception $e) {
                $this->messageManager->addError(__('Invalid date.'));
                $hasError = true;
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('An error occurred while saving Product Rule.'));

                $this->messageManager->addError($e->getMessage());
                $this->_getSession()->setPageData($data);
                $this->_redirect('adminhtml/*/edit', array('id' => $this->getRequest()->getParam('rule_id')));
                return;
            }

            if ($hasError) {
                $this->_getSession()->setFormData($data);
            }

            if ($hasError || $redirectBack) {
                $redirectPath = '*/*/edit';
                $redirectParams['id'] = $model->getId();
            }
        }
        $this->_redirect($redirectPath, $redirectParams);
    }
}
