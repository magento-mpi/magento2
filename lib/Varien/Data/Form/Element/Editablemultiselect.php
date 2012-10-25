<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Form editable select element
 *
 * Element allows inline modification of textual data within select
 *
 * @category   Varien
 * @package    Varien_Data
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Varien_Data_Form_Element_Editablemultiselect extends Varien_Data_Form_Element_Multiselect
{
    /**
     * Retrieve HTML markup of the element
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = parent::getElementHtml();

        $selectConfig = $this->getData('select_config');
        if ($this->getData("disabled")) {
            $selectConfig['is_entity_editable'] = false;
        }

        $selectConfigJson = Zend_Json::encode($selectConfig);
        $jsObjectName = $this->getJsObjectName();
        $html .= '<script type="text/javascript">'
            . '/*<![CDATA[*/'
            . '(function($) { $().ready(function () { '
            . "var {$jsObjectName} = new EditableMultiselect({$selectConfigJson}); "
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
