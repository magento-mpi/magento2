<?php
/**
 * Wishlist customer sharing block
 *
 * @package    Mage
 * @subpackage Wishlist
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Wishlist_Block_Customer_Sharing extends Mage_Core_Block_Template
{
	protected  $_enteredData = null;
	
	public function __construct()
	{
		$this->setTemplate('wishlist/sharing.phtml');
	}

	public function getSendUrl()
	{
		return $this->getUrl('*/*/send');
	}
	
	public function getEnteredData($key) 
	{
		if(is_null($this->_enteredData)) {
			$this->_enteredData = Mage::getSingleton('wishlist/session')->getData('sharing_form', true);
		}
		
		if(!$this->_enteredData || !isset($this->_enteredData[$key])) {
			return null;
		} else {
			return htmlspecialchars($this->_enteredData[$key]);
		}
	}
}// Class Mage_Wishlist_Block_Customer_Sharing END