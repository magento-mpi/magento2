<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml Manage Widgets Instance Controller
 */
namespace Magento\Widget\Controller\Adminhtml\Widget;

class Instance extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Widget\Model\Widget\InstanceFactory
     */
    protected $_widgetFactory;

    /**
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\Math\Random
     */
    protected $mathRandom;

    /**
     * @var \Magento\Core\Model\Translate
     */
    protected $_translator;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Math\Random $mathRandom
     * @param \Magento\Core\Model\Translate $translator
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
        \Magento\Logger $logger,
        \Magento\Math\Random $mathRandom,
        \Magento\Core\Model\Translate $translator
    ) {
        $this->_translator = $translator;
        $this->_coreRegistry = $coreRegistry;
        $this->_widgetFactory = $widgetFactory;
        $this->_logger = $logger;
        $this->mathRandom = $mathRandom;
        parent::__construct($context);
    }

    /**
     * Load layout, set active menu and breadcrumbs
     *
     * @return \Magento\Widget\Controller\Adminhtml\Widget\Instance
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Widget::cms_widget_instance')
            ->_addBreadcrumb(__('CMS'),
                __('CMS'))
            ->_addBreadcrumb(__('Manage Widget Instances'),
                __('Manage Widget Instances'));
        return $this;
    }

    /**
     * Init widget instance object and set it to registry
     *
     * @return \Magento\Widget\Model\Widget\Instance|boolean
     */
    protected function _initWidgetInstance()
    {
        $this->_title->add(__('Frontend Apps'));

        /** @var $widgetInstance \Magento\Widget\Model\Widget\Instance */
        $widgetInstance = $this->_widgetFactory->create();

        $code = $this->getRequest()->getParam('code', null);
        $instanceId = $this->getRequest()->getParam('instance_id', null);
        if ($instanceId) {
            $widgetInstance
                ->load($instanceId)
                ->setCode($code);
            if (!$widgetInstance->getId()) {
                $this->messageManager->addError(
                    __('Please specify a correct widget.')
                );
                return false;
            }
        } else {
            // Widget id was not provided on the query-string.  Locate the widget instance
            // type (namespace\classname) based upon the widget code (aka, widget id).
            $themeId = $this->getRequest()->getParam('theme_id', null);
            $type = $code != null ? $widgetInstance->getWidgetReference('code', $code, 'type') : null;
            $widgetInstance
                ->setType($type)
                ->setCode($code)
                ->setThemeId($themeId);
        }
        $this->_coreRegistry->register('current_widget_instance', $widgetInstance);
        return $widgetInstance;
    }

    /**
     * Widget Instances Grid
     *
     */
    public function indexAction()
    {
        $this->_title->add(__('Frontend Apps'));

        $this->_initAction();
        $this->_view->renderLayout();
    }

    /**
     * New widget instance action (forward to edit action)
     *
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit widget instance action
     *
     */
    public function editAction()
    {
        $widgetInstance = $this->_initWidgetInstance();
        if (!$widgetInstance) {
            $this->_redirect('adminhtml/*/');
            return;
        }

        $this->_title->add($widgetInstance->getId() ? $widgetInstance->getTitle() : __('New Frontend App Instance'));

        $this->_initAction();
        $this->_view->renderLayout();
    }

    /**
     * Set body to response
     *
     * @param string $body
     * @return null
     */
    protected function setBody($body)
    {
        $this->_translator->processResponseBody($body);

        $this->getResponse()->setBody($body);
    }

    /**
     * Validate action
     *
     */
    public function validateAction()
    {
        $response = new \Magento\Object();
        $response->setError(false);
        $widgetInstance = $this->_initWidgetInstance();
        $result = $widgetInstance->validate();
        if ($result !== true && is_string($result)) {
            $this->messageManager->addError($result);
            $this->_view->getLayout()->initMessages();
            $response->setError(true);
            $response->setMessage($this->_view->getLayout()->getMessagesBlock()->getGroupedHtml());
        }
        $this->setBody($response->toJson());
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        $widgetInstance = $this->_initWidgetInstance();
        if (!$widgetInstance) {
            $this->_redirect('adminhtml/*/');
            return;
        }
        $widgetInstance->setTitle($this->getRequest()->getPost('title'))
            ->setStoreIds($this->getRequest()->getPost('store_ids', array(0)))
            ->setSortOrder($this->getRequest()->getPost('sort_order', 0))
            ->setPageGroups($this->getRequest()->getPost('widget_instance'))
            ->setWidgetParameters($this->getRequest()->getPost('parameters'));
        try {
            $widgetInstance->save();
            $this->messageManager->addSuccess(
                __('The widget instance has been saved.')
            );
            if ($this->getRequest()->getParam('back', false)) {
                    $this->_redirect('adminhtml/*/edit', array(
                        'instance_id' => $widgetInstance->getId(),
                        '_current' => true
                    ));
            } else {
                $this->_redirect('adminhtml/*/');
            }
            return;
        } catch (\Exception $exception) {
            $this->messageManager->addError($exception->getMessage());
            $this->_logger->logException($exception);
            $this->_redirect('adminhtml/*/edit', array('_current' => true));
            return;
        }
        $this->_redirect('adminhtml/*/');
        return;
    }

    /**
     * Delete Action
     *
     */
    public function deleteAction()
    {
        $widgetInstance = $this->_initWidgetInstance();
        if ($widgetInstance) {
            try {
                $widgetInstance->delete();
                $this->messageManager->addSuccess(
                    __('The widget instance has been deleted.')
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('adminhtml/*/');
        return;
    }

    /**
     * Categories chooser Action (Ajax request)
     *
     */
    public function categoriesAction()
    {
        $selected = $this->getRequest()->getParam('selected', '');
        $isAnchorOnly = $this->getRequest()->getParam('is_anchor_only', 0);
        $chooser = $this->_view->getLayout()
            ->createBlock('Magento\Catalog\Block\Adminhtml\Category\Widget\Chooser')
            ->setUseMassaction(true)
            ->setId($this->mathRandom->getUniqueHash('categories'))
            ->setIsAnchorOnly($isAnchorOnly)
            ->setSelectedCategories(explode(',', $selected));
        $this->setBody($chooser->toHtml());
    }

    /**
     * Products chooser Action (Ajax request)
     *
     */
    public function productsAction()
    {
        $selected = $this->getRequest()->getParam('selected', '');
        $productTypeId = $this->getRequest()->getParam('product_type_id', '');
        $chooser = $this->_view->getLayout()
            ->createBlock('Magento\Catalog\Block\Adminhtml\Product\Widget\Chooser')
            ->setName($this->mathRandom->getUniqueHash('products_grid_'))
            ->setUseMassaction(true)
            ->setProductTypeId($productTypeId)
            ->setSelectedProducts(explode(',', $selected));
        /* @var $serializer \Magento\Backend\Block\Widget\Grid\Serializer */
        $serializer = $this->_view->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Grid\Serializer',
            '',
            array(
                'data' => array(
                    'grid_block' => $chooser,
                    'callback' => 'getSelectedProducts',
                    'input_element_name' => 'selected_products',
                    'reload_param_name' => 'selected_products'
                )
            )
        );
        $this->setBody($chooser->toHtml() . $serializer->toHtml());
    }

    /**
     * Blocks Action (Ajax request)
     *
     */
    public function blocksAction()
    {
        $this->_objectManager->get('Magento\App\State')
            ->emulateAreaCode('frontend', array($this, 'renderPageContainers'));
    }

    /**
     * Render page containers
     */
    public function renderPageContainers()
    {
        /* @var $widgetInstance \Magento\Widget\Model\Widget\Instance */
        $widgetInstance = $this->_initWidgetInstance();
        $layout = $this->getRequest()->getParam('layout');
        $selected = $this->getRequest()->getParam('selected', null);
        $blocksChooser = $this->_view->getLayout()
            ->createBlock('Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser\Container')
            ->setValue($selected)
            ->setArea($widgetInstance->getArea())
            ->setTheme($widgetInstance->getThemeId())
            ->setLayoutHandle($layout)
            ->setAllowedContainers($widgetInstance->getWidgetSupportedContainers());
        $this->setBody($blocksChooser->toHtml());
    }

    /**
     * Templates Chooser Action (Ajax request)
     *
     */
    public function templateAction()
    {
        /* @var $widgetInstance \Magento\Widget\Model\Widget\Instance */
        $widgetInstance = $this->_initWidgetInstance();
        $block = $this->getRequest()->getParam('block');
        $selected = $this->getRequest()->getParam('selected', null);
        $templateChooser = $this->_view->getLayout()
            ->createBlock('Magento\Widget\Block\Adminhtml\Widget\Instance\Edit\Chooser\Template')
            ->setSelected($selected)
            ->setWidgetTemplates($widgetInstance->getWidgetSupportedTemplatesByContainer($block));
        $this->setBody($templateChooser->toHtml());
    }

    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Widget::widget_instance');
    }
}
