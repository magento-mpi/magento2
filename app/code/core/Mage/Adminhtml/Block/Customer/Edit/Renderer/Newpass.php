<?php
/**
 * Customer new password field renderer
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Renderer_Newpass extends Mage_Core_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '<span class="field-row">';
        $html.= $element->getLabelHtml();
        $html.= $element->getElementHtml();
        $html.= '</span>'."\n";
        $html.= '<span class="field-row">';
        $html.= '<label>&nbsp;</label>';
        $html.= __('or');
        $html.= '</span>'."\n";
        $html.= '<span class="field-row">';
        $html.= '<label>&nbsp;</label>';
        $html.= '<input type="checkbox" name="'.$element->getName().'" value="auto" onclick="setElementDisable(\''.$element->getHtmlId().'\', this.checked)"/>&nbsp;';
        $html.= '<label class="normal">'.__('Send auto-generated password').'</label>';
        $html.= '</span>'."\n";

        return $html;
    }
}
