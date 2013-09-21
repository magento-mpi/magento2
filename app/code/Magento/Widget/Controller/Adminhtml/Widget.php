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
 * Widgets management controller
 *
 * @category    Magento
 * @package     Magento_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Widget_Controller_Adminhtml_Widget extends Magento_Adminhtml_Controller_Action
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * @var Magento_Widget_Model_Widget_Config
     */
    protected $_widgetConfig;

    /**
     * @var Magento_Widget_Model_Widget
     */
    protected $_widget;

    /**
     * @var Magento_Widget_Model_Widget_Config
     */
    protected $_widgetConfig;

    /**
     * @var Magento_Widget_Model_Widget
     */
    protected $_widget;

    /**
     * @param Magento_Widget_Model_Widget_Config $widgetConfig
     * @param Magento_Widget_Model_Widget $widget
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Widget_Model_Widget_Config $widgetConfig
     * @param Magento_Widget_Model_Widget $widget
     */
    public function __construct(
        Magento_Widget_Model_Widget_Config $widgetConfig,
        Magento_Widget_Model_Widget $widget,
        Magento_Backend_Controller_Context $context,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Widget_Model_Widget_Config $widgetConfig,
        Magento_Widget_Model_Widget $widget
    ) {
        $this->_widgetConfig = $widgetConfig;
        $this->_widget = $widget;
        $this->_coreRegistry = $coreRegistry;
        $this->_widgetConfig = $widgetConfig;
        $this->_widget = $widget;
        parent::__construct($context);
    }

    /**
     * Wisywyg widget plugin main page
     */
    public function indexAction()
    {
        // save extra params for widgets insertion form
        $skipped = $this->getRequest()->getParam('skip_widgets');
        $skipped = $this->_widgetConfig->decodeWidgetsFromQuery($skipped);

        $this->_coreRegistry->register('skip_widgets', $skipped);

        $this->loadLayout('empty')->renderLayout();
    }

    /**
     * Ajax responder for loading plugin options form
     */
    public function loadOptionsAction()
    {
        try {
            $this->loadLayout('empty');
            if ($paramsJson = $this->getRequest()->getParam('widget')) {
                $request = $this->_objectManager->get('Magento_Core_Helper_Data')->jsonDecode($paramsJson);
                if (is_array($request)) {
                    $optionsBlock = $this->getLayout()->getBlock('wysiwyg_widget.options');
                    if (isset($request['widget_type'])) {
                        $optionsBlock->setWidgetType($request['widget_type']);
                    }
                    if (isset($request['values'])) {
                        $optionsBlock->setWidgetValues($request['values']);
                    }
                }
                $this->renderLayout();
            }
        } catch (Magento_Core_Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($result));
        }
    }

    /**
     * Format widget pseudo-code for inserting into wysiwyg editor
     */
    public function buildWidgetAction()
    {
        $type = $this->getRequest()->getPost('widget_type');
        $params = $this->getRequest()->getPost('parameters', array());
        $asIs = $this->getRequest()->getPost('as_is');
        $html = $this->_widget->getWidgetDeclaration($type, $params, $asIs);
        $this->getResponse()->setBody($html);
    }
}
