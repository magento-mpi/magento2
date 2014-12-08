<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Controller\Adminhtml\Index;

class Delete extends \Magento\CustomerSegment\Controller\Adminhtml\Index
{
    /**
     * Delete customer segment
     *
     * @return void
     */
    public function execute()
    {
        try {
            $model = $this->_initSegment('id', true);
            $model->delete();
            $this->messageManager->addSuccess(__('You deleted the segment.'));
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('customersegment/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError(__("We're unable to delete the segement."));
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        }
        $this->_redirect('customersegment/*/');
    }
}
