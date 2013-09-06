<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Form editable select element
 *
 * Element allows inline modification of textual data within select
 *
 * @category   Magento
 * @package    Magento_Data
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Data_Form_Element_Editablemultiselect extends Magento_Data_Form_Element_Multiselect
{
    /**
     * Name of the default JavaScript class that is used to make multiselect editable
     *
     * This class must define init() method and receive configuration in the constructor
     */
    const DEFAULT_ELEMENT_JS_CLASS = 'EditableMultiselect';

    /**
     * Retrieve HTML markup of the element
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = parent::getElementHtml();

        $selectConfig = $this->getData('select_config');
        if ($this->getData('disabled')) {
            $selectConfig['is_entity_editable'] = false;
        }

        $elementJsClass = self::DEFAULT_ELEMENT_JS_CLASS;
        if ($this->getData('element_js_class')) {
            $elementJsClass = $this->getData('element_js_class');
        }

        $selectConfigJson = Zend_Json::encode($selectConfig);
        $jsObjectName = $this->getJsObjectName();
        $html .= '<script type="text/javascript">'
            . '/*<![CDATA[*/'
            . '(function($) { $().ready(function () { '
            . "var {$jsObjectName} = new {$elementJsClass}({$selectConfigJson}); "
            . "{$jsObjectName}.init(); }); })(jQuery);"
            . '/*]]>*/'
            . '</script>';
        return $html;
    }

    /**
     * Retrieve HTML markup of given select option
     *
     * @param array $option
     * @param array $selected
     * @return string
     */
    protected function _optionToHtml($option, $selected)
    {
        $html = '<option value="' . $this->_escape($option['value']) . '"';
        $html .= isset($option['title']) ? 'title="' . $this->_escape($option['title']) . '"' : '';
        $html .= isset($option['style']) ? 'style="' . $option['style'] . '"' : '';
        if (in_array((string)$option['value'], $selected)) {
            $html .= ' selected="selected"';
        }

        if ($this->getData('disabled')) {
            // if element is disabled then no data modification is allowed
            $html .= ' disabled="disabled" data-is-removable="no" data-is-editable="no"';
        }

        $html .= '>' . $this->_escape($option['label']) . '</option>' . "\n";
        return $html;
    }
}
