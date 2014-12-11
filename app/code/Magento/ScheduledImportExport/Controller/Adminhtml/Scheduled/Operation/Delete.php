<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation;

class Delete extends \Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation
{
    /**
     * Delete operation action
     *
     * @return void
     */
    public function execute()
    {
        $request = $this->getRequest();
        $id = (int)$request->getParam('id');
        if ($id) {
            try {
                $this->_objectManager->create(
                    'Magento\ScheduledImportExport\Model\Scheduled\Operation'
                )->setId(
                    $id
                )->delete();
                $this->messageManager->addSuccess(
                    $this->_objectManager->get(
                        'Magento\ScheduledImportExport\Helper\Data'
                    )->getSuccessDeleteMessage(
                        $request->getParam('type')
                    )
                );
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
                $this->messageManager->addError(__('Something sent wrong deleting the scheduled operation.'));
            }
        }
        $this->_redirect('adminhtml/scheduled_operation/index');
    }
}
