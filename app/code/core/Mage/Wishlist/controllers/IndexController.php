<?php
/**
 * Wishlist front controller
 *
 * @package    Mage
 * @subpackage Wishlist
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Wishlist_IndexController extends Mage_Core_Controller_Front_Action 
{
	public function preDispatch()
	{
		parent::preDispatch();
               
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
	}
	
	public function indexAction()
	{
		
	}
	
	public function addAction()
	{
		try {
			$wishlist = Mage::getModel('wishlist/wishlist')
				->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
		} 
		catch (Exception $e) {
			Mage::getSingleton('wishlist/session')->addError('Cannot create wishlist');
		}
		
		try {
			$wishlist->addNewItem($this->getRequest()->getParam('product'));
		}
		catch (Exception $e) {
			Mage::getSingleton('wishlist/session')->addError('Cannot add item to wishlist');
			throw $e;
		}
		
		$this->_redirect('*');
	}
}// Class Mage_Wishlist_IndexController END