<?php
/**
 * Wishlist model resource
 *
 * @package    Mage
 * @subpackage Wishlist
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Wishlist_Model_Mysql4_Wishlist extends Mage_Core_Model_Mysql4_Abstract 
{
	protected $_customerIdFieldName = 'customer_id';
	
	protected function _construct()
	{
		$this->_init('wishlist/wishlist', 'wishlist_id');
	}
	
	public function getCustomerIdFieldName()
	{
		return $this->_customerIdFieldName;
	}
	
	public function setCustomerIdFieldName($fieldName)
	{
		$this->_customerIdFieldName = $fieldName;
		return $this;
	}
}// Class Mage_Wishlist_Model_Mysql4_Wishlist END