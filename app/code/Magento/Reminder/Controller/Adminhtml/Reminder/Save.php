<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reminder\Controller\Adminhtml\Reminder;

class Save extends \Magento\Reminder\Controller\Adminhtml\Reminder
{
    /**
     * Save reminder rule
     *
     * @return void
     */
    public function execute()
    {
        if ($data = $this->getRequest()->getPost()) {
            try {
                $redirectBack = $this->getRequest()->getParam('back', false);

                $model = $this->_initRule('rule_id');

                $inputFilter = new \Zend_Filter_Input(
                    ['from_date' => $this->_dateFilter, 'to_date' => $this->_dateFilter],
                    [],
                    $data
                );
                $data = $inputFilter->getUnescaped();

                $validateResult = $model->validateData(new \Magento\Framework\Object($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->messageManager->addError($errorMessage);
                    }
                    $this->_getSession()->setFormData($data);

                    $this->_redirect('adminhtml/*/edit', ['id' => $model->getId()]);
                    return;
                }

                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);

                $model->loadPost($data);
                $this->_getSession()->setPageData($model->getData());
                $model->save();

                $this->messageManager->addSuccess(__('You saved the reminder rule.'));
                $this->_getSession()->setPageData(false);

                if ($redirectBack) {
                    $this->_redirect('adminhtml/*/edit', ['id' => $model->getId(), '_current' => true]);
                    return;
                }
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_getSession()->setPageData($data);
                $this->_redirect('adminhtml/*/edit', ['id' => $model->getId()]);
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(__('We could not save the reminder rule.'));
                $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            }
        }
        $this->_redirect('adminhtml/*/');
    }
}
