<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation;

class MassChangeStatus extends \Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation
{
    /**
     * Batch change status action
     *
     * @return void
     */
    public function execute()
    {
        $request = $this->getRequest();
        $ids = $request->getParam('operation');
        $status = (bool)$request->getParam('status');

        if (is_array($ids)) {
            $ids = array_filter($ids, 'intval');
            try {
                $operations = $this->_objectManager->create(
                    'Magento\ScheduledImportExport\Model\Resource\Scheduled\Operation\Collection'
                );
                $operations->addFieldToFilter($operations->getResource()->getIdFieldName(), ['in' => $ids]);

                foreach ($operations as $operation) {
                    $operation->setStatus($status)->save();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been updated.', count($operations))
                );
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
                $this->messageManager->addError(__('We cannot change status for all items.'));
            }
        }
        $this->_redirect('adminhtml/scheduled_operation/index');
    }
}
