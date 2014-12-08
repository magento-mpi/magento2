<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation;

class MassDelete extends \Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation
{
    /**
     * Batch delete action
     *
     * @return void
     */
    public function execute()
    {
        $request = $this->getRequest();
        $ids = $request->getParam('operation');
        if (is_array($ids)) {
            $ids = array_filter($ids, 'intval');
            try {
                $operations = $this->_objectManager->create(
                    'Magento\ScheduledImportExport\Model\Resource\Scheduled\Operation\Collection'
                );
                $operations->addFieldToFilter($operations->getResource()->getIdFieldName(), ['in' => $ids]);
                foreach ($operations as $operation) {
                    $operation->delete();
                }
                $this->messageManager->addSuccess(__('We deleted a total of %1 record(s).', count($operations)));
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
                $this->messageManager->addError(__('We cannot delete all items.'));
            }
        }
        $this->_redirect('adminhtml/scheduled_operation/index');
    }
}
