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

class Widget extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Core_Model_Registry $coreRegistry
    ) {
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
        $skipped = \Mage::getSingleton('Magento\Widget\Model\Widget\Config')->decodeWidgetsFromQuery($skipped);

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
                $request = $this->_objectManager->get('Magento\Core\Helper\Data')->jsonDecode($paramsJson);
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
        $html = \Mage::getSingleton('Magento\Widget\Model\Widget')->getWidgetDeclaration($type, $params, $asIs);
        $this->getResponse()->setBody($html);
    }
}
