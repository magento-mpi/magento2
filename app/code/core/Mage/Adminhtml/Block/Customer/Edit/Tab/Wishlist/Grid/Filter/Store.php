<?php
/**
 * Adminhtml customer wishlist grid filter by store
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Customer_Edit_Tab_Wishlist_Grid_Filter_Store extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select 
{
	protected function _getOptions()
	{
		$options = Mage::registry('stores_select_collection')->toOptionArray();
		array_unshift($options, array('label'=>'','value'=>''));
		return $options;
	}
	
	public function getCondition()
	{	
		return $this->getValue();
	}
}// Class Mage_Adminhtml_Block_Customer_Edit_Tab_Wishlist_Grid_Filter_Store END