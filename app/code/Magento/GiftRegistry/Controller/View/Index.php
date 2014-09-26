<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Controller\View;

class Index extends \Magento\GiftRegistry\Controller\View
{
    /**
     * View giftregistry list in 'My Account' section
     *
     * @return void
     */
    public function execute()
    {
        $entity = $this->_objectManager->create('Magento\GiftRegistry\Model\Entity');
        $entity->loadByUrlKey($this->getRequest()->getParam('id'));
        if (!$entity->getId() || !$entity->getCustomerId() || !$entity->getTypeId() || !$entity->getIsActive()) {
            $this->_forward('noroute');
            return;
        }

        /** @var \Magento\Customer\Model\Customer */
        $customer = $this->_objectManager->create('Magento\Customer\Model\Customer');
        $customer->load($entity->getCustomerId());
        $entity->setCustomer($customer);
        $this->_coreRegistry->register('current_entity', $entity);

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->getPage()->getConfig()->setTitle(__('Gift Registry Info'));
        $this->_view->renderLayout();
    }
}
