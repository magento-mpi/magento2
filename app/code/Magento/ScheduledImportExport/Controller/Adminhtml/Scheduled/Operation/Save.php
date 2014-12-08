<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation;

class Save extends \Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation
{
    /**
     * Save operation action
     *
     * @return void
     */
    public function execute()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();

            if (isset(
                $data['id']
            ) && !is_numeric(
                $data['id']
            ) || !isset(
                $data['id']
            ) && (!isset(
                $data['operation_type']
            ) || empty($data['operation_type'])) || !is_array(
                $data['start_time']
            )
            ) {
                $this->messageManager->addError(__("We couldn't save the scheduled operation."));
                $this->_redirect('adminhtml/*/*', ['_current' => true]);

                return;
            }
            $data['start_time'] = join(':', $data['start_time']);
            if (isset($data['export_filter']) && is_array($data['export_filter'])) {
                $data['entity_attributes']['export_filter'] = $data['export_filter'];
                if (isset($data['skip_attr']) && is_array($data['skip_attr'])) {
                    $data['entity_attributes']['skip_attr'] = array_filter($data['skip_attr'], 'intval');
                }
            }

            try {
                /** @var \Magento\ScheduledImportExport\Model\Scheduled\Operation $operation */
                $operation = $this->_objectManager->create('Magento\ScheduledImportExport\Model\Scheduled\Operation');
                $operation->setData($data);
                $operation->save();
                $this->messageManager->addSuccess(
                    $this->_objectManager->get(
                        'Magento\ScheduledImportExport\Helper\Data'
                    )->getSuccessSaveMessage(
                        $operation->getOperationType()
                    )
                );
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
                $this->messageManager->addError(__("We couldn't save the scheduled operation."));
            }
        }
        $this->_redirect('adminhtml/scheduled_operation/index');
    }
}
