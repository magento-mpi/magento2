<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Controller\Index;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Model\Exception;

class UpdateItems extends \Magento\GiftRegistry\Controller\Index
{
    /**
     * Update gift registry items
     *
     * @return void|ResponseInterface
     */
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->_redirect('*/*/');
        }

        try {
            $entity = $this->_initEntity();
            if ($entity->getId()) {
                $items = $this->getRequest()->getParam('items');
                $entity->updateItems($items);
                $this->messageManager->addSuccess(__('You updated the gift registry items.'));
            }
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('*/*/');
            return;
        } catch (\Magento\Framework\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__("We couldn't update the gift registry."));
        }
        $this->_redirect('*/*/items', ['_current' => true]);
    }
}
