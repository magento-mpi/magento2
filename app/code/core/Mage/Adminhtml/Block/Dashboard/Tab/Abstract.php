<?php
/**
 * Adminhtml dashboard tab abstract
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Dashboard_Tab_Abstract extends Mage_Adminhtml_Block_Widget
{
	/**
	 * Block data collection
	 *
	 * @var Varien_Data_Collection_Db
	 */
	protected $_collection = null;
	
	public function getCollection()
	{
		return $this->_collection;
	}
	
	public function setCollection($collection) 
	{
		$this->_collection = $collection;
		return $this;
	}
	
}// Class Mage_Adminhtml_Block_Dashboard_Abstract END