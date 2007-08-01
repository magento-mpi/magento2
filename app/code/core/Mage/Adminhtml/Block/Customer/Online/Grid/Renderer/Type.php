<?php
/**
 * Adminhtml customers online grid renderer for customer type.
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Customer_Online_Grid_Renderer_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		return ($row->getData($this->getColumn()->getIndex()) > 0 ) ? __('Customer') : __('Visitor') ;
	}

}