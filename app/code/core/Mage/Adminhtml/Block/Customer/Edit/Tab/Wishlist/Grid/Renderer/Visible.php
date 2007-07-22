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

class Mage_Adminhtml_Block_Customer_Edit_Tab_Wishlist_Grid_Renderer_Visible extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row) 
	{
		return implode(", ", $this->_getSharedStoresNames($row->getData($this->getColumn()->getIndex())));
	}
	
	protected function _getSharedStoresNames($storeId) 
	{
		$collection = Mage::registry('stores_select_collection');
		$store = $collection->getItemById($storeId);
		$sharedIds = $store->getDatashareStores('wishlist');
		
		$sharedNames = array();
		
		foreach($sharedIds as $sharedId) {
			$sharedNames[] = $collection->getItemById($sharedId)->getName();
		}
		
		return $sharedNames;
	}
}// Class Mage_Adminhtml_Block_Customer_Edit_Tab_Wishlist_Grid_Renderer_Visible END