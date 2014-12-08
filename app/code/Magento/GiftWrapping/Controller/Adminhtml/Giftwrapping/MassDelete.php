<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping;

class MassDelete extends \Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping
{
    /**
     * Delete specified gift wrapping(s)
     * This action can be performed on 'Manage Gift Wrappings' page
     *
     * @return void
     */
    public function execute()
    {
        $wrappingIds = (array)$this->getRequest()->getParam('wrapping_ids');
        if (!is_array($wrappingIds)) {
            $this->messageManager->addError(__('Please select items.'));
        } else {
            try {
                $wrappingCollection = $this->_objectManager->create(
                    'Magento\GiftWrapping\Model\Resource\Wrapping\Collection'
                );
                $wrappingCollection->addFieldToFilter('wrapping_id', ['in' => $wrappingIds]);
                foreach ($wrappingCollection as $wrapping) {
                    $wrapping->delete();
                }
                $this->messageManager->addSuccess(__('You deleted a total of %1 records.', count($wrappingIds)));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        $this->_redirect('adminhtml/*/index');
    }
}
