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
 * Widget model for different purposes
 *
 * @category    Magento
 * @package     Magento_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Widget\Model;

class Widget extends \Magento\Object
{
    /**
     * @var \Magento\Core\Model\Config\Modules\Reader
     */
    protected $_configReader;

    /**
     * @var \Magento\Core\Model\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * @var \Magento\Core\Model\View\Url
     */
    protected $_viewUrl;

    /**
     * @var \Magento\Core\Model\View\FileSystem
     */
    protected $_viewFileSystem;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param \Magento\Core\Model\Config\Modules\Reader $configReader
     * @param \Magento\Core\Model\Cache\Type\Config $configCacheType
     * @param \Magento\Core\Model\View\Url $viewUrl
     * @param \Magento\Core\Model\View\FileSystem $viewFileSystem
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        \Magento\Core\Model\Config\Modules\Reader $configReader,
        \Magento\Core\Model\Cache\Type\Config $configCacheType,
        \Magento\Core\Model\View\Url $viewUrl,
        \Magento\Core\Model\View\FileSystem $viewFileSystem,
        array $data = array()
    ) {
        $this->_coreData = $coreData;
        parent::__construct($data);
        $this->_configReader = $configReader;
        $this->_configCacheType = $configCacheType;
        $this->_viewUrl = $viewUrl;
        $this->_viewFileSystem = $viewFileSystem;
    }

    /**
     * Load Widgets XML config from widget.xml files and cache it
     *
     * @return \Magento\Simplexml\Config
     */
    public function getXmlConfig()
    {
        $cacheId = 'widget_config';
        $cachedXml = $this->_configCacheType->load($cacheId);
        if ($cachedXml) {
            $xmlConfig = new \Magento\Simplexml\Config($cachedXml);
        } else {
            $xmlConfig = new \Magento\Simplexml\Config();
            $xmlConfig->loadString('<?xml version="1.0"?><widgets></widgets>');
            $this->_configReader->loadModulesConfiguration('widget.xml', $xmlConfig);
            $this->_configCacheType->save($xmlConfig->getXmlString(), $cacheId);
        }
        return $xmlConfig;
    }

    /**
     * Return widget XML config element based on its type
     *
     * @param string $type Widget type
     * @return null|\Magento\Simplexml\Element
     */
    public function getXmlElementByType($type)
    {
        $elements = $this->getXmlConfig()->getXpath('*[@type="' . $type . '"]');
        if (is_array($elements) && isset($elements[0]) && $elements[0] instanceof \Magento\Simplexml\Element) {
            return $elements[0];
        }
        return null;
    }

    /**
     * Wrapper for getXmlElementByType method
     *
     * @param string $type Widget type
     * @return null|\Magento\Simplexml\Element
     */
    public function getConfigAsXml($type)
    {
        return $this->getXmlElementByType($type);
    }

    /**
     * Return widget XML configuration as \Magento\Object and makes some data preparations
     *
     * @param string $type Widget type
     * @return \Magento\Object
     */
    public function getConfigAsObject($type)
    {
        $xml = $this->getConfigAsXml($type);

        $object = new \Magento\Object();
        if ($xml === null) {
            return $object;
        }

        // Save all nodes to object data
        $object->setType($type);
        $object->setData($xml->asCanonicalArray());

        // Correct widget parameters and convert its data to objects
        $params = $object->getData('parameters');
        $newParams = array();
        if (is_array($params)) {
            $sortOrder = 0;
            foreach ($params as $key => $data) {
                if (is_array($data)) {
                    $data['key'] = $key;
                    $data['sort_order'] = isset($data['sort_order']) ? (int)$data['sort_order'] : $sortOrder;

                    // prepare values (for drop-dawns) specified directly in configuration
                    $values = array();
                    if (isset($data['values']) && is_array($data['values'])) {
                        foreach ($data['values'] as $value) {
                            if (isset($value['label']) && isset($value['value'])) {
                                $values[] = $value;
                            }
                        }
                    }
                    $data['values'] = $values;

                    // prepare helper block object
                    if (isset($data['helper_block'])) {
                        $helper = new \Magento\Object();
                        if (isset($data['helper_block']['data']) && is_array($data['helper_block']['data'])) {
                            $helper->addData($data['helper_block']['data']);
                        }
                        if (isset($data['helper_block']['type'])) {
                            $helper->setType($data['helper_block']['type']);
                        }
                        $data['helper_block'] = $helper;
                    }

                    $newParams[$key] = new \Magento\Object($data);
                    $sortOrder++;
                }
            }
        }
        uasort($newParams, array($this, '_sortParameters'));
        $object->setData('parameters', $newParams);

        return $object;
    }

    /**
     * Return filtered list of widgets as SimpleXml object
     *
     * @param array $filters Key-value array of filters for widget node properties
     * @return \Magento\Simplexml\Element
     */
    public function getWidgetsXml($filters = array())
    {
        $widgets = $this->getXmlConfig()->getNode();
        $result = clone $widgets;

        // filter widgets by params
        if (is_array($filters) && count($filters) > 0) {
            foreach ($widgets as $code => $widget) {
                try {
                    $reflection = new \ReflectionObject($widget);
                    foreach ($filters as $field => $value) {
                        if (!$reflection->hasProperty($field) || (string)$widget->{$field} != $value) {
                            throw new \Exception();
                        }
                    }
                } catch (\Exception $e) {
                    unset($result->{$code});
                    continue;
                }
            }
        }

        return $result;
    }

    /**
     * Return list of widgets as array
     *
     * @param array $filters Key-value array of filters for widget node properties
     * @return array
     */
    public function getWidgetsArray($filters = array())
    {
        if (!$this->_getData('widgets_array')) {
            $result = array();
            foreach ($this->getWidgetsXml($filters) as $widget) {
                $result[$widget->getName()] = array(
                    'name'          => __((string)$widget->name),
                    'code'          => $widget->getName(),
                    'type'          => $widget->getAttribute('type'),
                    'description'   => __((string)$widget->description)
                );
            }
            usort($result, array($this, "_sortWidgets"));
            $this->setData('widgets_array', $result);
        }
        return $this->_getData('widgets_array');
    }

    /**
     * Return widget presentation code in WYSIWYG editor
     *
     * @param string $type Widget Type
     * @param array $params Pre-configured Widget Params
     * @param bool $asIs Return result as widget directive(true) or as placeholder image(false)
     * @return string Widget directive ready to parse
     */
    public function getWidgetDeclaration($type, $params = array(), $asIs = true)
    {
        $directive = '{{widget type="' . $type . '"';

        foreach ($params as $name => $value) {
            // Retrieve default option value if pre-configured
            if (is_array($value)) {
                $value = implode(',', $value);
            } elseif (trim($value) == '') {
                $widget = $this->getConfigAsObject($type);
                $parameters = $widget->getParameters();
                if (isset($parameters[$name]) && is_object($parameters[$name])) {
                    $value = $parameters[$name]->getValue();
                }
            }
            if ($value) {
                $directive .= sprintf(' %s="%s"', $name, $value);
            }
        }
        $directive .= '}}';

        if ($asIs) {
            return $directive;
        }

        $html = sprintf('<img id="%s" src="%s" title="%s">',
            $this->_idEncode($directive),
            $this->getPlaceholderImageUrl($type),
            $this->_coreData->escapeUrl($directive)
        );
        return $html;
    }

    /**
     * Get image URL of WYSIWYG placeholder image
     *
     * @param string $type
     * @return string
     */
    public function getPlaceholderImageUrl($type)
    {
        $placeholder = false;
        $widgetXml = $this->getConfigAsXml($type);
        if (is_object($widgetXml)) {
            $placeholder = (string)$widgetXml->placeholder_image;
        }
        if (!$placeholder || !$this->_viewFileSystem->getViewFile($placeholder)) {
            $placeholder = 'Magento_Widget::placeholder.gif';
        }
        return $this->_viewUrl->getViewFileUrl($placeholder);
    }

    /**
     * Get a list of URLs of WYSIWYG placeholder images
     *
     * array(<type> => <url>)
     *
     * @return array
     */
    public function getPlaceholderImageUrls()
    {
        $result = array();
        /** @var \Magento\Simplexml\Element $widget */
        foreach ($this->getXmlConfig()->getNode() as $widget) {
            $type = (string)$widget->getAttribute('type');
            $result[$type] = $this->getPlaceholderImageUrl($type);
        }
        return $result;
    }

    /**
     * Return list of required JS files to be included on the top of the page before insertion plugin loaded
     *
     * @return array
     */
    public function getWidgetsRequiredJsFiles()
    {
        $result = array();
        foreach ($this->getWidgetsXml() as $widget) {
            if ($widget->js) {
                foreach (explode(',', (string)$widget->js) as $js) {
                    $result[] = $js;
                }
            }
        }
        return $result;
    }

    /**
     * Encode string to valid HTML id element, based on base64 encoding
     *
     * @param string $string
     * @return string
     */
    protected function _idEncode($string)
    {
        return strtr(base64_encode($string), '+/=', ':_-');
    }

    /**
     * User-defined widgets sorting by Name
     *
     * @param array $firstElement
     * @param array $secondElement
     * @return boolean
     */
    protected function _sortWidgets($firstElement, $secondElement)
    {
        return strcmp($firstElement["name"], $secondElement["name"]);
    }

    /**
     * Widget parameters sort callback
     *
     * @param \Magento\Object $firstElement
     * @param \Magento\Object $secondElement
     * @return int
     */
    protected function _sortParameters($firstElement, $secondElement)
    {
        $aOrder = (int)$firstElement->getData('sort_order');
        $bOrder = (int)$secondElement->getData('sort_order');
        return $aOrder < $bOrder ? -1 : ($aOrder > $bOrder ? 1 : 0);
    }
}
