<?php
/**
 * description
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Newsletter_Queue_Edit_Form_Renderer_Template  extends Mage_Core_Block_Abstract  implements Varien_Data_Form_Element_Renderer_Interface
{
	public function render(Varien_Data_Form_Element_Abstract $element) 
	{
		return '<span class="field-row">'."\n" 
			   . '<label for="' . $element->getHtmlId() . '">'.$element->getLabel().'</label>'."\n"
			   . '<span id="' . $element->getHtmlId() . '">' . $this->getEscaped($element->getValue()->getTemplateSubject()) . '</span>'
			   . '</span>';
	}
	
	public function getEscaped($value) 
	{
		return htmlspecialchars($value);
	}
}// Class Mage_Adminhtml_Block_Newsletter_Queue_Edit_Form_Renderer_Template END