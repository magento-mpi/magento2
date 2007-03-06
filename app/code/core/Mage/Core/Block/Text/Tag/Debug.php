<?php



/**
 * Base html block
 *
 * @copyright  Varien, 2007
 * @version    1.0 
 * @author	   Moshe Gurvich <moshe@varien.com>
 * @date       Thu Feb 08 05:56:43 EET 2007
 */

class Mage_Core_Block_Text_Tag_Debug extends Mage_Core_Block_Text_Tag
{
	function __construct()
	{
		parent::__construct();

		$this->setAttribute(array(
		  'tagName'=>'xmp',
		));
	}
	
	function setValue($value)
	{
	    return $this->setContents(print_r($value, 1));
	}
}// Class Mage_Core_Block_List END