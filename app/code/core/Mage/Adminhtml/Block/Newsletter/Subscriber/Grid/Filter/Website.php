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
		$result = $this->getCollection()->toOptionArray();
		array_unshift($result, array('label'=>null, 'value'=>null));
		return $result;
	}
	
	public function getCollection() 
	{
		if(is_null($this->_websiteCollection)) {
			$this->_websiteCollection = Mage::getResourceModel('core/website_collection')
				->load();
		}
		
		Mage::register('website_collection', $this->_websiteCollection);
		
		return $this->_websiteCollection;
	}
	
	public function getCondition()
	{
		
		$id = $this->getValue();
		if(!$id) {
			return null;
		}
		
		$website = Mage::getModel('core/website')
			->load($id);
		
		return array('in'=>$website->getStoresIds(true));
	}
}// Class Mage_Adminhtml_Block_Newsletter_Subscriber_Grid_Filter_Website END