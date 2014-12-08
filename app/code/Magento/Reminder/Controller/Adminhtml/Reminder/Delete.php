<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Controller\Adminhtml\Reminder;

class Delete extends \Magento\Reminder\Controller\Adminhtml\Reminder
{
    /**
     * Delete reminder rule
     *
     * @return void
     */
    public function execute()
    {
        try {
            $model = $this->_initRule();
            $model->delete();
            $this->messageManager->addSuccess(__('You deleted the reminder rule.'));
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('adminhtml/*/edit', ['id' => $model->getId()]);
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We could not delete the reminder rule.'));
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        }
        $this->_redirect('adminhtml/*/');
    }
}
