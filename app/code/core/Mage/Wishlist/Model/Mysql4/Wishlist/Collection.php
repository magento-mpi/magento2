<?php
/**
 * Wislist model collection
 *
 * @package    Mage
 * @subpackage Wishlist
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Wishlist_Model_Mysql4_Wishlist extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		$this->_init('wishlist/wishlist');
	}
}// Class Mage_Wishlist_Model_Mysql4_Wishlist END