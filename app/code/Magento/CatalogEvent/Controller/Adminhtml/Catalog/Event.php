<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Events Adminhtml controller
 *
 * @category   Magento
 * @package    Magento_CatalogEvent
 */

namespace Magento\CatalogEvent\Controller\Adminhtml\Catalog;

class Event extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Check is enabled module in config
     *
     * @return \Magento\CatalogEvent\Controller\Adminhtml\Catalog\Event
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!\Mage::helper('Magento\CatalogEvent\Helper\Data')->isEnabled()) {
            if ($this->getRequest()->getActionName() != 'noroute') {
                $this->_forward('noroute');
            }
        }
        return $this;
    }

    /**
     * Init action breadcrumbs and active menu
     *
     * @return Magento_CatalogEvent_IndexController
     */
    public function _initAction()
    {
        $this->loadLayout()
            ->_addBreadcrumb(
                __('Catalog'),
                __('Catalog')
            )
            ->_addBreadcrumb(
                __('Events'),
                __('Events')
            )
            ->_setActiveMenu('Magento_CatalogEvent::catalog_magento_catalogevent_events');
        return $this;
    }

    /**
     * Events list action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_title(__('Events'));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * New event action
     *
     * @return void
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit event action
     */
    public function editAction()
    {
        $this->_title(__('Events'));

        $event = \Mage::getModel('\Magento\CatalogEvent\Model\Event')
            ->setStoreId($this->getRequest()->getParam('store', 0));
        if ($eventId = $this->getRequest()->getParam('id', false)) {
            $event->load($eventId);
        } else {
            $event->setCategoryId($this->getRequest()->getParam('category_id'));
        }

        $this->_title($event->getId() ? sprintf("#%s", $event->getId()) : __('New Event'));

        $sessionData = \Mage::getSingleton('Magento\Adminhtml\Model\Session')->getEventData(true);
        if (!empty($sessionData)) {
            $event->addData($sessionData);
        }

        \Mage::register('magento_catalogevent_event', $event);

        $this->_initAction();
        $layout = $this->getLayout();
        $layout->getBlock('head')->setCanLoadExtJs(true);
        if (($switchBlock = $layout->getBlock('store_switcher'))) {
            if (!$event->getId() || \Mage::app()->isSingleStoreMode()) {
                $layout->unsetChild($layout->getParentName('store_switcher'), 'store_switcher');
            } else {
                $switchBlock->setDefaultStoreName(__('Default Values'))
                    ->setSwitchUrl($this->getUrl('*/*/*', array('_current' => true, 'store' => null)));
            }
        }
        $this->renderLayout();

    }

    /**
     * Save action
     *
     * @return void
     */
    public function saveAction()
    {
        $event = \Mage::getModel('\Magento\CatalogEvent\Model\Event')
            ->setStoreId($this->getRequest()->getParam('store', 0));
        /* @var $event \Magento\CatalogEvent\Model\Event */
        if ($eventId = $this->getRequest()->getParam('id', false)) {
            $event->load($eventId);
        } else {
            $event->setCategoryId($this->getRequest()->getParam('category_id'));
        }

        $postData = $this->_filterPostData($this->getRequest()->getPost());

        if (!isset($postData['catalogevent'])) {
            $this->_getSession()->addError(
                __('Something went wrong while saving this event.')
            );
            $this->_redirect('*/*/edit', array('_current' => true));
            return;
        }

        $data = new \Magento\Object($postData['catalogevent']);

        $event->setDisplayState($data->getDisplayState())
            ->setStoreDateStart($data->getDateStart())
            ->setStoreDateEnd($data->getDateEnd())
            ->setSortOrder($data->getSortOrder());

        $isUploaded = true;
        try {
            $uploader = new \Magento\Core\Model\File\Uploader('image');
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setAllowCreateFolders(true);
            $uploader->setFilesDispersion(false);
        } catch (\Exception $e) {
            $isUploaded = false;
        }

        $validateResult = $event->validate();
        if ($validateResult !== true) {
            foreach ($validateResult as $errorMessage) {
                $this->_getSession()->addError($errorMessage);
            }
            $this->_getSession()->setEventData($event->getData());
            $this->_redirect('*/*/edit', array('_current' => true));
            return;
        }

        try {
            if ($data->getData('image/is_default')) {
                $event->setImage(null);
            } elseif ($data->getData('image/delete')) {
                $event->setImage('');
            } elseif ($isUploaded) {
                try {
                    $event->setImage($uploader);
                } catch (\Exception $e) {
                    \Mage::throwException(
                        __('We did not upload your image.')
                    );
                }
            }
            $event->save();

            $this->_getSession()->addSuccess(
                __('You saved the event.')
            );
            if ($this->getRequest()->getParam('back') == 'edit') {
                $this->_redirect('*/*/edit', array('_current' => true, 'id' => $event->getId()));
            } else {
                $this->_redirect('*/*/');
            }
        } catch (\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_getSession()->setEventData($event->getData());
            $this->_redirect('*/*/edit', array('_current' => true));
        }


    }

    /**
     * Delete action
     *
     * @return void
     */
    public function deleteAction()
    {
        $event = \Mage::getModel('\Magento\CatalogEvent\Model\Event');
        $event->load($this->getRequest()->getParam('id', false));
        if ($event->getId()) {
            try {
                $event->delete();
                $this->_getSession()->addSuccess(
                    __('You deleted the event.')
                );
                if ($this->getRequest()->getParam('category')) {
                    $this->_redirect('*/catalog_category/edit', array('id' => $event->getCategoryId(), 'clear' => 1));
                } else {
                    $this->_redirect('*/*/');
                }
            } catch (\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('_current' => true));
            }
        }
    }

    /**
     * Ajax categories tree loader action
     *
     */
    public function categoriesJsonAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('\Magento\CatalogEvent\Block\Adminhtml\Event\Edit\Category')
                ->getTreeArray($id, true, 1)
        );
    }

    /**
     * Acl check for admin
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_CatalogEvent::events');
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        if(isset($data['catalogevent'])) {
            $_data = $data['catalogevent'];
            $_data = $this->_filterDateTime($_data, array('date_start', 'date_end'));
            $data['catalogevent'] = $_data;
        }
        return $data;
    }

}
