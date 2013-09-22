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
 * Template Filter Model
 *
 * @category    Magento
 * @package     Magento_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Widget\Model\Template;

class Filter extends \Magento\Cms\Model\Template\Filter
{
    /** @var  \Magento\Widget\Model\Widget */
    protected $_widget;

    /** @var  \Magento\Widget\Model\Resource\Widget */
    protected $_widgetResource;

    /** @var  \Magento\Core\Model\App */
    protected $_coreApp;

    /**
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Widget\Model\Widget $widget
     * @param \Magento\Widget\Model\Resource\Widget $widgetResource
     * @param \Magento\Core\Model\App $coreApp
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\View\Url $viewUrl
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     */
    public function __construct(
        \Magento\Core\Model\Logger $logger,
        \Magento\Widget\Model\Widget $widget,
        \Magento\Widget\Model\Resource\Widget $widgetResource,
        \Magento\Core\Model\App $coreApp,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\View\Url $viewUrl,
        \Magento\Core\Model\Store\Config $coreStoreConfig
    ) {
        $this->_widget = $widget;
        $this->_widgetResource = $widgetResource;
        $this->_coreApp = $coreApp;
        parent::__construct($logger, $coreData, $viewUrl, $coreStoreConfig);
    }
    /**
     * Generate widget
     *
     * @param array $construction
     * @return string
     */
    public function widgetDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);

        // Determine what name block should have in layout
        $name = null;
        if (isset($params['name'])) {
            $name = $params['name'];
        }

        // validate required parameter type or id
        if (!empty($params['type'])) {
            $type = $params['type'];
        } elseif (!empty($params['id'])) {
            $preconfigured = $this->_widgetResource->loadPreconfiguredWidget($params['id']);
            $type = $preconfigured['widget_type'];
            $params = $preconfigured['parameters'];
        } else {
            return '';
        }
        
        // we have no other way to avoid fatal errors for type like 'cms/widget__link', '_cms/widget_link' etc. 
        $xml = $this->_widget->getWidgetByClassType($type);
        if ($xml === null) {
            return '';
        }
        
        /**
         * define widget block and check the type is instance of Widget Interface
         * @var \Magento\Core\Block\Abstract $widget
         */
        $widget = $this->_coreApp->getLayout()->createBlock($type, $name, array('data' => $params));
        if (!$widget instanceof \Magento\Widget\Block\BlockInterface) {
            return '';
        }

        return $widget->toHtml();
    }
}
