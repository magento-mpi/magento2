<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Controller\Adminhtml\Reminder;

class Run extends \Magento\Reminder\Controller\Adminhtml\Reminder
{
    /**
     * Match reminder rule and send emails for matched customers
     *
     * @return void
     */
    public function execute()
    {
        try {
            $model = $this->_initRule();
            $model->sendReminderEmails();
            $this->messageManager->addSuccess(__('You matched the reminder rule.'));
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Reminder rule matching error.'));
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        }
        $this->_redirect('adminhtml/*/edit', ['id' => $model->getId(), 'active_tab' => 'matched_customers']);
    }
}
