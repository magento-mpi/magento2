<?php
/**
 * Adminhtml customers online grid block item renderer by ip.
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Customer_Online_Grid_Renderer_Ip extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
	
	public function render(Varien_Object $row) 
	{
		return long2ip($row->getData($this->getColumn()->getIndex()));
	}
	
}// Class Mage_Adminhtml_Block_Customer_Online_Grid_Renderer_Ip END