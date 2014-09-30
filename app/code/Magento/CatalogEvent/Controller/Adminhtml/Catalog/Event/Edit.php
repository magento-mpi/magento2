<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogEvent\Controller\Adminhtml\Catalog\Event;

use Magento\CatalogEvent\Model\Event as ModelEvent;

class Edit extends \Magento\CatalogEvent\Controller\Adminhtml\Catalog\Event
{
    /**
     * Edit event action
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Events'));

        /** @var ModelEvent $event */
        $event = $this->_eventFactory->create()->setStoreId($this->getRequest()->getParam('store', 0));
        $eventId = $this->getRequest()->getParam('id', false);
        if ($eventId) {
            $event->load($eventId);
        } else {
            $event->setCategoryId($this->getRequest()->getParam('category_id'));
        }

        $this->_title->add($event->getId() ? sprintf("#%s", $event->getId()) : __('New Event'));

        $sessionData = $this->_getSession()->getEventData(true);
        if (!empty($sessionData)) {
            $event->addData($sessionData);
        }

        $this->_coreRegistry->register('magento_catalogevent_event', $event);

        $this->_initAction();
        $layout = $this->_view->getLayout();
        if ($switchBlock = $layout->getBlock('store_switcher')) {
            if (!$event->getId() || $this->_storeManager->isSingleStoreMode()) {
                $layout->unsetChild($layout->getParentName('store_switcher'), 'store_switcher');
            } else {
                $switchBlock->setDefaultStoreName(
                    __('Default Values')
                )->setSwitchUrl(
                    $this->getUrl('adminhtml/*/*', array('_current' => true, 'store' => null))
                );
            }
        }
        $this->_view->renderLayout();
    }
}
