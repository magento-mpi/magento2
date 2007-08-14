<?php
/**
 * Adminhtml dashboard tab grath axis
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Dashboard_Tab_Graph_Axis extends Mage_Core_Block_Abstract
{
	protected $_maximumValueCache = null;
	
	public function getMaximumValue()
	{
		if(is_null($this->_maximumValueCache)) {
			foreach ($this->getParentBlock()->getCollection() as $item) 
			{
				
			}
		}
	}
	
	public function getField() {
		
	}
}// Class Mage_Adminhtml_Block_Dashboard_Tab_Graph_Axis END