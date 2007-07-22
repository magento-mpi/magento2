<?php
/**
 * Wishlist shared items controllers
 *
 * @package    Mage
 * @subpackage Wishlist
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Wishlist_SharedController extends Mage_Core_Controller_Front_Action
{
	public function indexAction() 
	{
		$this->loadLayout();
		$this->renderLayout();
	}
}// Class Mage_Wishlist_SharedController END