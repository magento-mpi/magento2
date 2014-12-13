<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftRegistry\Controller\Index;

use Magento\Framework\Model\Exception;

class Share extends \Magento\GiftRegistry\Controller\Index
{
    /**
     * Share selected gift registry entity
     *
     * @return void
     */
    public function execute()
    {
        try {
            $entity = $this->_initEntity();
            $this->_view->loadLayout();
            $this->_view->getLayout()->initMessages();
            $this->_view->getPage()->getConfig()->getTitle()->set(__('Share Gift Registry'));
            $this->_view->getLayout()->getBlock('giftregistry.customer.share')->setEntity($entity);
            $this->_view->renderLayout();
            return;
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $message = __('Something went wrong while sharing the gift registry.');
            $this->messageManager->addException($e, $message);
        }
        $this->_redirect('*/*/');
    }
}
