<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract class for grid widgets
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
abstract class Saas_PrintedTemplate_Block_Widget_AbstractGrid
    extends Magento_Backend_Block_Template
    implements Mage_Widget_Block_Interface
{
    /**
     * Widget ID cache, don't use it directly
     *
     * @var string
     */
    protected $_id;

    /**
     * Map widget properties to CSS properties
     *
     * Format:
     *      array('key' => array('CSS property' => 'Widget property'))
     * or:
     *      array('key' => array('CSS property' => array('Widget property', 'converter')))
     * where formater is string that corresponds function _converCss<Converter>()
     *
     * @var array
     */
    protected $_styleMap = array();

    /**
     * Returns value for CSS property font-style
     *
     * @param bool $italic Is font italic
     * @return string Value of CSS property
     */
    protected function _convertCssFontStyle($italic)
    {
        return $italic ? 'italic' : 'normal';
    }

    /**
     * Returns value for font-weight CSS property
     *
     * @param bool $bold Is font bold
     * @return string
     */
    protected function _convertCssFontWeight($bold)
    {
        return $bold ? 'bold' : 'normal';
    }

    /**
     * Returns value for font-size CSS property
     *
     * @param string|int $size Size in points
     * @return string
     */
    protected function _convertCssSizePt($size)
    {
        return $size ? $size . 'pt' : '';
    }

    /**
     * Returns value for font-family CSS property
     *
     * @param string $fontFamily
     * @return string
     */
    protected function _convertCssFontFamily($fontFamily)
    {
        $fonts = Mage::getModel('Saas_PrintedTemplate_Model_Config')->getFontsArray();

        return isset($fonts[$fontFamily]['css']) ? $fonts[$fontFamily]['css'] : '';
    }

    /**
     * Returns CSS for selector using map by $key
     *
     * Using map by key maps properties of the widget to CSS properties
     * and returns compiled CSS for selector.
     *
     * @param string $selector CSS selector for element
     * @param string $key Key of map
     * @return string <selector> {<compiled style>}
     */
    public function getStyle($selector, $key)
    {
        $styles = '';
        if (isset($this->_styleMap[$key])) {
            foreach ($this->_styleMap[$key] as $cssProperty => $widgetProperity) {
                if (is_array($widgetProperity)) {
                    list ($property, $converter) = $widgetProperity;
                    $converterMethod = '_convertCss' . uc_words($converter, '');
                    $cssValue = $this->$converterMethod($this->_getData($property));
                } else {
                    $cssValue = $this->_getData($widgetProperity);
                }
                $styles .=  "$cssProperty: $cssValue; ";
            }
        }

        return "$selector { $styles}";
    }

    /**
     * Generate unique ID for widget
     *
     * @return string
     */
    public function generateUniqueId()
    {
        return str_replace('0.', '', str_replace(' ', '_', microtime()));
    }

    /**
     * Get unique widget ID
     */
    public function getWidgetId()
    {
        if (null === $this->_id) {
            $this->_id = 'widget_' . $this->generateUniqueId();
        }

        return $this->_id;
    }

    /**
     * Get helper instance
     *
     * @return Saas_PrintedTemplate_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('Saas_PrintedTemplate_Helper_Data');
    }

    /**
     * Get entity type
     *
     * @return string|false
     */
    public function getEntityType()
    {
        if ($this->hasEntity()) {
            return $this->getEntity()->getType();
        }

        return false;
    }
}
