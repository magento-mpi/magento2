<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Credentials Archive form element renderer
 */
class Enterprise_Pbridge_Block_Adminhtml_System_Config_Kount_FieldFile
    extends Mage_Backend_Block_System_Config_Form_Field
{
    /**
     * Render form elemrnt
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '';
        if ((string)$element->getValue()) {
            $html = Mage::helper('Enterprise_Pbridge_Helper_Data')->__('File uploaded') . ' ';
        }

        $element->setClass('input-file');
        $element->setType('file');
        $html .= $element->getElementHtml();
        $html .= $this->_getDeleteCheckbox($element);

        return $html;
    }

    /**
     * Render delete checkbox
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getDeleteCheckbox(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '';
        if ($element->getValue()) {
            $html .= '<div>';
            $html .= '<input type="checkbox" name="' . $element->getName() . '[delete]"'
                .' value="1" class="checkbox" id="' . $element->getHtmlId() . '_delete"'
                . ($element->getDisabled() ? ' disabled="disabled"': '') . '/>';
            $html .= '<label for="' . $element->getHtmlId() . '_delete"'
                . ($element->getDisabled() ? ' class="disabled"' : '') .'> Delete</label>';
            $html .= '<input type="hidden" name="'.$element->getName().'[value]" value="" />';
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Render table row
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $id = $element->getHtmlId();

        $html = '<tr id="row_' . $id . '">'
            . '<td class="label"><label for="' . $id . '">' . $element->getLabel() . '</label></td>';

        $html .= '<td class="value">';
        $html .= $this->_getElementHtml($element);
        if ($element->getComment()) {
            $html .= '<p class="note"><span>' . $element->getComment() . '</span></p>';
        }
        $html .= '</td>';

        $html .= '<td class="scope-label">';
        if ($element->getScope()) {
            $html .= $element->getScopeLabel();
        }
        $html .= '</td>';

        $html .= '<td class="">';
        if ($element->getHint()) {
            $html .= '<div class="hint" >';
            $html .= '<div style="display: none;">' . $element->getHint() . '</div>';
            $html .= '</div>';
        }
        $html .= '</td>';

        $html .= '</tr>';
        return $html;
    }
}
