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
namespace Magento\Widget\Controller\Adminhtml;

use Magento\Backend\App\Action;

class Widget extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Widget\Model\Widget\Config
     */
    protected $_widgetConfig;

    /**
     * @var \Magento\Widget\Model\Widget
     */
    protected $_widget;

    /**
     * @param \Magento\Widget\Model\Widget\Config $widgetConfig
     * @param \Magento\Widget\Model\Widget $widget
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Widget\Model\Widget\Config $widgetConfig,
        \Magento\Widget\Model\Widget $widget,
        Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_widgetConfig = $widgetConfig;
        $this->_widget = $widget;
        $this->_coreRegistry = $coreRegistry;
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

        $this->_layoutServices->loadLayout('empty')->renderLayout();
    }

    /**
     * Ajax responder for loading plugin options form
     */
    public function loadOptionsAction()
    {
        try {
            $this->_layoutServices->loadLayout('empty');
            if ($paramsJson = $this->getRequest()->getParam('widget')) {
                $request = $this->_objectManager->get('Magento\Core\Helper\Data')->jsonDecode($paramsJson);
                if (is_array($request)) {
                    $optionsBlock = $this->_layoutServices->getLayout()->getBlock('wysiwyg_widget.options');
                    if (isset($request['widget_type'])) {
                        $optionsBlock->setWidgetType($request['widget_type']);
                    }
                    if (isset($request['values'])) {
                        $optionsBlock->setWidgetValues($request['values']);
                    }
                }
                $this->_layoutServices->renderLayout();
            }
        } catch (\Magento\Core\Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->setBody($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result));
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
