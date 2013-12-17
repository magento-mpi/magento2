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
namespace Magento\CatalogEvent\Controller\Adminhtml\Catalog;

class Event extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * Event model factory
     *
     * @var \Magento\CatalogEvent\Model\EventFactory
     */
    protected $_eventFactory;

    /**
     * @var \Magento\Core\Filter\DateTime
     */
    protected $_dateTimeFilter;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\CatalogEvent\Model\EventFactory $eventFactory
     * @param \Magento\Core\Filter\DateTime $dateTimeFilter
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\CatalogEvent\Model\EventFactory $eventFactory,
        \Magento\Core\Filter\DateTime $dateTimeFilter,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        $this->_eventFactory = $eventFactory;
        $this->_dateTimeFilter = $dateTimeFilter;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @param \Magento\App\RequestInterface $request
     * @return \Magento\App\ResponseInterface
     */
    public function dispatch(\Magento\App\RequestInterface $request)
    {
        if (!$this->_objectManager->get('Magento\CatalogEvent\Helper\Data')->isEnabled()) {
            if ($this->getRequest()->getActionName() != 'noroute') {
                $this->_forward('noroute');
                return parent::dispatch($request);
            }
        }
        return parent::dispatch($request);
    }

    /**
     * Init action breadcrumbs and active menu
     *
     * @return \Magento\CatalogEvent\Controller\Adminhtml\Catalog\Event
     */
    public function _initAction()
    {
        $this->_view->loadLayout();
        $this->_addBreadcrumb(__('Catalog'), __('Catalog'));
        $this->_addBreadcrumb(__('Events'), __('Events'));
        $this->_setActiveMenu('Magento_CatalogEvent::catalog_magento_catalogevent_events');
        return $this;
    }

    /**
     * Events list action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_title->add(__('Events'));
        $this->_initAction();
        $this->_view->renderLayout();
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
        $this->_title->add(__('Events'));

        /** @var \Magento\CatalogEvent\Model\Event $event */
        $event = $this->_eventFactory->create()
            ->setStoreId($this->getRequest()->getParam('store', 0));
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
        $layout->getBlock('head')->setCanLoadExtJs(true);
        if (($switchBlock = $layout->getBlock('store_switcher'))) {
            if (!$event->getId() || $this->_storeManager->isSingleStoreMode()) {
                $layout->unsetChild($layout->getParentName('store_switcher'), 'store_switcher');
            } else {
                $switchBlock->setDefaultStoreName(__('Default Values'))
                    ->setSwitchUrl($this->getUrl('adminhtml/*/*', array('_current' => true, 'store' => null)));
            }
        }
        $this->_view->renderLayout();
    }

    /**
     * Save action
     *
     * @throws \Magento\Core\Exception
     */
    public function saveAction()
    {
        /* @var \Magento\CatalogEvent\Model\Event $event*/
        $event = $this->_eventFactory->create()->setStoreId($this->getRequest()->getParam('store', 0));
        $eventId = $this->getRequest()->getParam('id', false);
        if ($eventId) {
            $event->load($eventId);
        } else {
            $event->setCategoryId($this->getRequest()->getParam('category_id'));
        }

        $postData = $this->_filterPostData($this->getRequest()->getPost());

        if (!isset($postData['catalogevent'])) {
            $this->messageManager->addError(
                __('Something went wrong while saving this event.')
            );
            $this->_redirect('adminhtml/*/edit', array('_current' => true));
            return;
        }

        $data = new \Magento\Object($postData['catalogevent']);

        $event->setDisplayState($data->getDisplayState())
            ->setStoreDateStart($data->getDateStart())
            ->setStoreDateEnd($data->getDateEnd())
            ->setSortOrder($data->getSortOrder());

        $isUploaded = true;
        try {
            $uploader = $this->_objectManager
                ->create('Magento\Core\Model\File\Uploader', array('fileId' => 'image'));;
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
                $this->messageManager->addError($errorMessage);
            }
            $this->_getSession()->setEventData($event->getData());
            $this->_redirect('adminhtml/*/edit', array('_current' => true));
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
                    throw new \Magento\Core\Exception(__('We did not upload your image.'));
                }
            }
            $event->save();

            $this->messageManager->addSuccess(
                __('You saved the event.')
            );
            if ($this->getRequest()->getParam('back') == 'edit') {
                $this->_redirect('adminhtml/*/edit', array('_current' => true, 'id' => $event->getId()));
            } else {
                $this->_redirect('adminhtml/*/');
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_getSession()->setEventData($event->getData());
            $this->_redirect('adminhtml/*/edit', array('_current' => true));
        }
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        /** @var \Magento\CatalogEvent\Model\Event $event */
        $event = $this->_eventFactory->create();
        $event->load($this->getRequest()->getParam('id', false));
        if ($event->getId()) {
            try {
                $event->delete();
                $this->messageManager->addSuccess(
                    __('You deleted the event.')
                );
                if ($this->getRequest()->getParam('category')) {
                    $this->_redirect('adminhtml/category/edit', array('id' => $event->getCategoryId(), 'clear' => 1));
                } else {
                    $this->_redirect('adminhtml/*/');
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', array('_current' => true));
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
            $this->_view->getLayout()->createBlock('Magento\CatalogEvent\Block\Adminhtml\Event\Edit\Category')
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
            $inputFilter = new \Zend_Filter_Input(
                array(
                    'date_start' => $this->_dateTimeFilter,
                    'date_end' => $this->_dateTimeFilter,
                ),
                array(),
                $data['catalogevent']
            );
            $data['catalogevent'] = $inputFilter->getUnescaped();
        }
        return $data;
    }
}
