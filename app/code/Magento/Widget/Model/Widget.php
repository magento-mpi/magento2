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
class Magento_Widget_Model_Widget
{
    /**
     * @var Magento_Widget_Model_Config_Data
     */
    protected $_dataStorage;

    /**
     * @var Magento_Core_Model_View_Url
     */
    protected $_viewUrl;

    /**
     * @var Magento_Core_Model_View_FileSystem
     */
    protected $_viewFileSystem;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /** @var  array */
    protected $_widgetsArray = array();

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Widget_Model_Config_Data $dataStorage
     * @param Magento_Core_Model_View_Url $viewUrl
     * @param Magento_Core_Model_View_FileSystem $viewFileSystem
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Widget_Model_Config_Data $dataStorage,
        Magento_Core_Model_View_Url $viewUrl,
        Magento_Core_Model_View_FileSystem $viewFileSystem
    ) {
        $this->_coreData = $coreData;
        $this->_dataStorage = $dataStorage;
        $this->_viewUrl = $viewUrl;
        $this->_viewFileSystem = $viewFileSystem;
    }

    /**
     * Return widget config based on its class type
     *
     * @param string $type Widget type
     * @return null|array
     */
    public function getWidgetByClassType($type)
    {
        $widgets = $this->getWidgets();
        /** @var array $widget */
        foreach ($widgets as $widget) {
            if (isset($widget['@'])) {
                if (isset($widget['@']['type'])) {
                    if ($type === $widget['@']['type'])
                        return $widget;
                }
            }
        }
        return null;
    }

    /**
     * Return widget XML configuration as Magento_Object and makes some data preparations
     *
     * @param string $type Widget type
     * @return Magento_Object
     */
    public function getConfigAsObject($type)
    {
        $widget = $this->getWidgetByClassType($type);

        $object = new Magento_Object();
        if ($widget === null) {
            return $object;
        }
        $widget = $this->_getAsCanonicalArray($widget);

        // Save all nodes to object data
        $object->setType($type);
        $object->setData($widget);

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
                        $helper = new Magento_Object();
                        if (isset($data['helper_block']['data']) && is_array($data['helper_block']['data'])) {
                            $helper->addData($data['helper_block']['data']);
                        }
                        if (isset($data['helper_block']['type'])) {
                            $helper->setType($data['helper_block']['type']);
                        }
                        $data['helper_block'] = $helper;
                    }

                    $newParams[$key] = new Magento_Object($data);
                    $sortOrder++;
                }
            }
        }
        uasort($newParams, array($this, '_sortParameters'));
        $object->setData('parameters', $newParams);

        return $object;
    }

    /**
     * Return filtered list of widgets
     *
     * @param array $filters Key-value array of filters for widget node properties
     * @return array
     */
    public function getWidgets($filters = array())
    {
        $widgets = $this->_dataStorage->get();
        $result = $widgets;

        // filter widgets by params
        if (is_array($filters) && count($filters) > 0) {
            foreach ($widgets as $code => $widget) {
                try {
                    foreach ($filters as $field => $value) {
                        if (!isset($widget[$field]) || (string)$widget[$field] != $value) {
                            throw new Exception();
                        }
                    }
                } catch (Exception $e) {
                    unset($result[$code]);
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
        if (empty($this->_widgetsArray)) {
            $result = array();
            foreach ($this->getWidgets($filters) as $code => $widget) {
                $result[$widget['name']] = array(
                    'name'          => __((string)$widget['name']),
                    'code'          => $code,
                    'type'          => $widget['@']['type'],
                    'description'   => __((string)$widget['description'])
                );
            }
            usort($result, array($this, "_sortWidgets"));
            $this->_widgetsArray = $result;
        }
        return $this->_widgetsArray;
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
        $widget = $this->getWidgetByClassType($type);
        if (is_array($widget) && isset($widget['placeholder_image'])) {
            $placeholder = (string)$widget['placeholder_image'];
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
        $widgets = $this->getWidgets();
        /** @var array $widget */
        foreach ($widgets as $widget) {
            if (isset($widget['@'])) {
                if (isset($widget['@']['type'])) {
                    $type = $widget['@']['type'];
                    $result[$type] = $this->getPlaceholderImageUrl($type);
                }
            }
        }
        return $result;
    }

    /**
     * Remove attributes from widget array so that emulates how Magento_Simplexml_Element::asCanonicalArray works
     *
     * @param $inputArray
     * @return mixed
     */
    protected function _getAsCanonicalArray($inputArray)
    {
        if (array_key_exists('@', $inputArray)) {
            unset($inputArray['@']);
        }
        foreach ($inputArray as $key => $value) {
            if (!is_array($value)) {
                continue;
            }
            $inputArray[$key] = $this->_getAsCanonicalArray($value);
        }
        return $inputArray;

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
     * @param Magento_Object $firstElement
     * @param Magento_Object $secondElement
     * @return int
     */
    protected function _sortParameters($firstElement, $secondElement)
    {
        $aOrder = (int)$firstElement->getData('sort_order');
        $bOrder = (int)$secondElement->getData('sort_order');
        return $aOrder < $bOrder ? -1 : ($aOrder > $bOrder ? 1 : 0);
    }
}
