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

class Edit extends \Magento\GiftRegistry\Controller\Index
{
    /**
     * Select gift registry type action
     *
     * @return void
     */
    public function execute()
    {
        $typeId = $this->getRequest()->getParam('type_id');
        $entityId = $this->getRequest()->getParam('entity_id');
        try {
            if (!$typeId) {
                if (!$entityId) {
                    $this->_redirect('*/*/');
                    return;
                } else {
                    // editing existing entity
                    /* @var $model \Magento\GiftRegistry\Model\Entity */
                    $model = $this->_initEntity('entity_id');
                }
            }

            if ($typeId && !$entityId) {
                // creating new entity
                /* @var $model \Magento\GiftRegistry\Model\Entity */
                $model = $this->_objectManager->get('Magento\GiftRegistry\Model\Entity');
                if ($model->setTypeById($typeId) === false) {
                    throw new Exception(__('Please correct the gift registry.'));
                }
            }

            $this->_coreRegistry->register('magento_giftregistry_entity', $model);
            $this->_coreRegistry->register('magento_giftregistry_address', $model->exportAddress());

            $this->_view->loadLayout();
            $this->_view->getLayout()->initMessages();

            if ($model->getId()) {
                $pageTitle = __('Edit Gift Registry');
            } else {
                $pageTitle = __('Create Gift Registry');
            }
            $this->pageConfig->setTitle($pageTitle);
            $this->_view->renderLayout();
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('*/*/');
        }
    }
}
