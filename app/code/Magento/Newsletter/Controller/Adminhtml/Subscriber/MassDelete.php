<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Newsletter\Controller\Adminhtml\Subscriber;

class MassDelete extends \Magento\Newsletter\Controller\Adminhtml\Subscriber
{
    /**
     * Delete one or more subscribers action
     *
     * @return void
     */
    public function execute()
    {
        $subscribersIds = $this->getRequest()->getParam('subscriber');
        if (!is_array($subscribersIds)) {
            $this->messageManager->addError(__('Please select one or more subscribers.'));
        } else {
            try {
                foreach ($subscribersIds as $subscriberId) {
                    $subscriber = $this->_objectManager->create(
                        'Magento\Newsletter\Model\Subscriber'
                    )->load(
                        $subscriberId
                    );
                    $subscriber->delete();
                }
                $this->messageManager->addSuccess(__('Total of %1 record(s) were deleted', count($subscribersIds)));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }
}
