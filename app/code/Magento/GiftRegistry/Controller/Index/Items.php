<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Controller\Index;

use \Magento\Framework\Model\Exception;

class Items extends \Magento\GiftRegistry\Controller\Index
{
    /**
     * View items of selected gift registry entity
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->_coreRegistry->register('current_entity', $this->_initEntity());
            $this->_view->loadLayout();
            $this->_view->getLayout()->initMessages();
            $this->_view->getPage()->getConfig()->setTitle(__('Gift Registry Items'));
            $this->_view->renderLayout();
            return;
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
}
