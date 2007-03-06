<?php



/**
 * Base html block
 *
 * @copyright  Varien, 2007
 * @version    1.0 
 * @author	   Moshe Gurvich <moshe@varien.com>
 * @date       Thu Feb 08 05:56:43 EET 2007
 */

class Mage_Core_Block_Text_Tag_Js extends Mage_Core_Block_Text_Tag
{
	function __construct()
	{
		parent::__construct();
		
		$this->setAttribute(array(
		  'tagName'=>'script',
		  'tagParams'=>array('language'=>'javascript', 'type'=>'text/javascript'),
		));
	}
	
	function setSrc($src)
	{
	    return $this->setTagParam('src', Mage::getBaseUrl('js').$src);
	}
}// Class Mage_Core_Block_List END