<?php
/**
 * Adminhtml customers wishlist grid item renderer for item visibility
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Customer_Edit_Tab_Wishlist_Grid_Renderer_Description extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row) 
	{
		return nl2br(htmlspecialchars($row->getData($this->getColumn()->getIndex())));
	}
	
	
}// Class Mage_Adminhtml_Block_Customer_Edit_Tab_Wishlist_Grid_Renderer_Description END