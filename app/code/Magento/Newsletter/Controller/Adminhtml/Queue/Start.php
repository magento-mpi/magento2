<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Controller\Adminhtml\Queue;

class Start extends \Magento\Newsletter\Controller\Adminhtml\Queue
{
    /**
     * Start Newsletter queue
     *
     * @return void
     */
    public function execute()
    {
        $queue = $this->_objectManager->create(
            'Magento\Newsletter\Model\Queue'
        )->load(
            $this->getRequest()->getParam('id')
        );
        if ($queue->getId()) {
            if (!in_array(
                $queue->getQueueStatus(),
                array(\Magento\Newsletter\Model\Queue::STATUS_NEVER, \Magento\Newsletter\Model\Queue::STATUS_PAUSE)
            )
            ) {
                $this->_redirect('*/*');
                return;
            }

            $queue->setQueueStartAt(
                $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate()
            )->setQueueStatus(
                \Magento\Newsletter\Model\Queue::STATUS_SENDING
            )->save();
        }

        $this->_redirect('*/*');
    }
}
