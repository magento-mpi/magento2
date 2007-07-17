<?php
/**
 * Fieldset config form element renderer
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_System_Config_Form_Fieldset 
    extends Mage_Core_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $cId = $element->getContainer()->getHtmlId();
        $idPrefix = $cId.'_'.$element->getHtmlId();
        $html = '<h3>'.$element->getLegend().'</h3>';
        $html.= '<fieldset id="'.$element->getHtmlId().'"><legend>'.$element->getLegend().'</legend>';
        $html.= '<table cellspacing=0 border=1 cellpadding=2><thead><tr>';
        $html.= '<th>&nbsp;</th>'; // field label column
        $html.= '<th><input id="'.$idPrefix.'_inherit" name="'.$cId.'" type="radio">'; // default column
        $html.= '<label for="'.$idPrefix.'_inherit">'.__('Default').'</label></th>';
        $html.= '<th><input id="'.$idPrefix.'_custom" name="'.$cId.'" type="radio">'; // custom column
        $html.= '<label for="'.$idPrefix.'_custom">'.__('Custom').'</label></th>';
        $html.= '</tr></thead><tbody>';
        //$html.= $element->getElementHtml();
        foreach ($element->getElements() as $field) {
        	$html.= $field->toHtml();
        }
        $html.= '</tbody></table></fieldset>';
        return $html;
    }
}
