<?php
/**
 * Abstract config form element renderer
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_System_Config_Form_Field
    extends Mage_Core_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '<tr><td>'.$element->getLabel().'</td>';
        $html .= '<td><input type="radio">Default</td>';
        $html .= '<td><input type="radio">'.$element->getElementHtml().'</td></tr>';
        return $html;
    }
}
