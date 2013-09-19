<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Events Adminhtml controller
 */
class Magento_CatalogEvent_Controller_Adminhtml_Catalog_Event extends Magento_Adminhtml_Controller_Action
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * Store model manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Event model factory
     *
     * @var Magento_CatalogEvent_Model_EventFactory
     */
    protected $_eventFactory;

    /**
     * Construct
     *
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_CatalogEvent_Model_EventFactory $eventFactory
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_CatalogEvent_Model_EventFactory $eventFactory
    ) {
        parent::__construct($context);

        $this->_coreRegistry = $coreRegistry;
        $this->_storeManager = $storeManager;
        $this->_eventFactory = $eventFactory;
    }

    /**
     * Check is enabled module in config
     *
     * @return Magento_CatalogEvent_Controller_Adminhtml_Catalog_Event
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->_objectManager->get('Magento_CatalogEvent_Helper_Data')->isEnabled()) {
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
            ->_addBreadcrumb(__('Catalog'), __('Catalog'))
            ->_addBreadcrumb(__('Events'), __('Events'))
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

        /** @var Magento_CatalogEvent_Model_Event $event */
        $event = $this->_eventFactory->create()
            ->setStoreId($this->getRequest()->getParam('store', 0));
        $eventId = $this->getRequest()->getParam('id', false);
        if ($eventId) {
            $event->load($eventId);
        } else {
            $event->setCategoryId($this->getRequest()->getParam('category_id'));
        }

        $this->_title($event->getId() ? sprintf("#%s", $event->getId()) : __('New Event'));

        $sessionData = Mage::getSingleton('Magento_Adminhtml_Model_Session')->getEventData(true);
        if (!empty($sessionData)) {
            $event->addData($sessionData);
        }

        $this->_coreRegistry->register('magento_catalogevent_event', $event);

        $this->_initAction();
        $layout = $this->getLayout();
        $layout->getBlock('head')->setCanLoadExtJs(true);
        if (($switchBlock = $layout->getBlock('store_switcher'))) {
            if (!$event->getId() || $this->_storeManager->isSingleStoreMode()) {
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
     * @throws Magento_Core_Exception
     */
    public function saveAction()
    {
        /* @var Magento_CatalogEvent_Model_Event $event*/
        $event = $this->_eventFactory->create()->setStoreId($this->getRequest()->getParam('store', 0));
        $eventId = $this->getRequest()->getParam('id', false);
        if ($eventId) {
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

        $data = new Magento_Object($postData['catalogevent']);

        $event->setDisplayState($data->getDisplayState())
            ->setStoreDateStart($data->getDateStart())
            ->setStoreDateEnd($data->getDateEnd())
            ->setSortOrder($data->getSortOrder());

        $isUploaded = true;
        try {
            $uploader = $this->_objectManager
                ->create('Magento_Core_Model_File_Uploader', array('fileId' => 'image'));;
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setAllowCreateFolders(true);
            $uploader->setFilesDispersion(false);
        } catch (Exception $e) {
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
                } catch (Exception $e) {
                    throw new Magento_Core_Exception(__('We did not upload your image.'));
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
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_getSession()->setEventData($event->getData());
            $this->_redirect('*/*/edit', array('_current' => true));
        }
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        /** @var Magento_CatalogEvent_Model_Event $event */
        $event = $this->_eventFactory->create();
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
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('_current' => true));
            }
        }
    }

    /**
     * Ajax categories tree loader action
     */
    public function categoriesJsonAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('Magento_CatalogEvent_Block_Adminhtml_Event_Edit_Category')
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
     * @param array $data
     * @return array
     */
    protected function _filterPostData($data)
    {
        if (isset($data['catalogevent'])) {
            $_data = $data['catalogevent'];
            $_data = $this->_filterDateTime($_data, array('date_start', 'date_end'));
            $data['catalogevent'] = $_data;
        }
        return $data;
    }
}
