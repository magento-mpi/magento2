<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Controller\Index;

use Magento\Framework\Model\Exception;

class Delete extends \Magento\GiftRegistry\Controller\Index
{
    /**
     * Delete selected gift registry entity
     *
     * @return void
     */
    public function execute()
    {
        try {
            $entity = $this->_initEntity();
            if ($entity->getId()) {
                $entity->delete();
                $this->messageManager->addSuccess(__('You deleted this gift registry.'));
            }
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $message = __('Something went wrong while deleting the gift registry.');
            $this->messageManager->addException($e, $message);
        }
        $this->_redirect('*/*/');
    }
}
