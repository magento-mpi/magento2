<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping;

class ChangeStatus extends \Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping
{
    /**
     * Change gift wrapping(s) status action
     *
     * @return void
     */
    public function execute()
    {
        $wrappingIds = (array)$this->getRequest()->getParam('wrapping_ids');
        $status = (int)(bool)$this->getRequest()->getParam('status');
        try {
            $wrappingCollection = $this->_objectManager->create(
                'Magento\GiftWrapping\Model\Resource\Wrapping\Collection'
            );
            $wrappingCollection->addFieldToFilter('wrapping_id', array('in' => $wrappingIds));
            foreach ($wrappingCollection as $wrapping) {
                $wrapping->setStatus($status);
            }
            $wrappingCollection->save();
            $this->messageManager->addSuccess(__('You updated a total of %1 records.', count($wrappingIds)));
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while updating the wrapping(s) status.'));
        }

        $this->_redirect('adminhtml/*/index');
    }
}
