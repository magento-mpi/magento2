<?php
/**
 * Wishlist session model
 *
 * @package    Mage
 * @subpackage Wishlist
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Wishlist_Model_Session extends Mage_Core_Model_Session_Abstract 
{
	public function __construct()
	{
		parent::__construct();
		$this->init('wishlist');
	}
}// Class Mage_Wishlist_Model_Session END