<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Controller\Adminhtml\Promo\Quote;

class Save extends \Magento\SalesRule\Controller\Adminhtml\Promo\Quote
{
    /**
     * Promo quote save action
     *
     * @return void
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            try {
                /** @var $model \Magento\SalesRule\Model\Rule */
                $model = $this->_objectManager->create('Magento\SalesRule\Model\Rule');
                $this->_eventManager->dispatch(
                    'adminhtml_controller_salesrule_prepare_save',
                    array('request' => $this->getRequest())
                );
                $data = $this->getRequest()->getPost();
                $inputFilter = new \Zend_Filter_Input(
                    array('from_date' => $this->_dateFilter, 'to_date' => $this->_dateFilter),
                    array(),
                    $data
                );
                $data = $inputFilter->getUnescaped();
                $id = $this->getRequest()->getParam('rule_id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new \Magento\Framework\Model\Exception(__('The wrong rule is specified.'));
                    }
                }

                $session = $this->_objectManager->get('Magento\Backend\Model\Session');

                $validateResult = $model->validateData(new \Magento\Framework\Object($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->messageManager->addError($errorMessage);
                    }
                    $session->setPageData($data);
                    $this->_redirect('sales_rule/*/edit', array('id' => $model->getId()));
                    return;
                }

                if (isset(
                    $data['simple_action']
                ) && $data['simple_action'] == 'by_percent' && isset(
                    $data['discount_amount']
                )
                ) {
                    $data['discount_amount'] = min(100, $data['discount_amount']);
                }
                if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                }
                if (isset($data['rule']['actions'])) {
                    $data['actions'] = $data['rule']['actions'];
                }
                unset($data['rule']);
                $model->loadPost($data);

                $useAutoGeneration = (int)(!empty($data['use_auto_generation']));
                $model->setUseAutoGeneration($useAutoGeneration);

                $session->setPageData($model->getData());

                $model->save();
                $this->messageManager->addSuccess(__('The rule has been saved.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('sales_rule/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('sales_rule/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $id = (int)$this->getRequest()->getParam('rule_id');
                if (!empty($id)) {
                    $this->_redirect('sales_rule/*/edit', array('id' => $id));
                } else {
                    $this->_redirect('sales_rule/*/new');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('An error occurred while saving the rule data. Please review the log and try again.')
                );
                $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                $this->_redirect('sales_rule/*/edit', array('id' => $this->getRequest()->getParam('rule_id')));
                return;
            }
        }
        $this->_redirect('sales_rule/*/');
    }
}
