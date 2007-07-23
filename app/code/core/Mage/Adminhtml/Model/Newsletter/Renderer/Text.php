<?php
/**
 * Template text preview field renderer
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <ivan@varien.com>
 */
class Mage_Adminhtml_Model_Newsletter_Renderer_Text implements Varien_Data_Form_Element_Renderer_Interface
{
        
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '<span class="field-row">'."\n";
        if ($element->getLabel()) {
            $html.= '<label for="'.$element->getHtmlId().'">'.$element->getLabel().'</label>'."\n";
        }
        $html.= '<iframe src="' . $element->getValue() . '" id="' . $element->getHtmlId() . '" frameborder="0" class="template-preview"/>';
        $html.= '</span>'."\n";
        
        return $html;
    }
}