<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Controller\Adminhtml\Queue;

class Pause extends \Magento\Newsletter\Controller\Adminhtml\Queue
{
    /**
     * Pause Newsletter queue
     *
     * @return void
     */
    public function execute()
    {
        $queue = $this->_objectManager->get(
            'Magento\Newsletter\Model\Queue'
        )->load(
            $this->getRequest()->getParam('id')
        );

        if (!in_array($queue->getQueueStatus(), array(\Magento\Newsletter\Model\Queue::STATUS_SENDING))) {
            $this->_redirect('*/*');
            return;
        }

        $queue->setQueueStatus(\Magento\Newsletter\Model\Queue::STATUS_PAUSE);
        $queue->save();

        $this->_redirect('*/*');
    }
}
