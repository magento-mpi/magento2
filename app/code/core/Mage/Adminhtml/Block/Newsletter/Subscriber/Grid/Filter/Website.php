<?php
/**
 * Adminhtml newsletter subscribers grid website filter
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Newsletter_Subscriber_Grid_Filter_Website extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select 
{
	protected $_websiteCollection = null;
	
	protected function _getOptions() 
	{
		return $this->getCollection()->toOptionArray();
	}
	
	public function getCollection() 
	{
		if(is_null($this->_websiteCollection)) {
			$this->_websiteCollection = Mage::getResourceModel('core/website_collection')
				->load();
		}
		
		return $this->_websiteCollection;
	}
}// Class Mage_Adminhtml_Block_Newsletter_Subscriber_Grid_Filter_Website END