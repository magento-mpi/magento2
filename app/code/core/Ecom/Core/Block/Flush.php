<?php



/**
 * Immediate flush block. To be used only as root
 *
 * @copyright  Varien, 2007
 * @version    1.0 
 * @author     Moshe Gurvich <moshe@varien.com>
 * @author	   Soroka Dmitriy <dmitriy@varien.com>
 * @date       Thu Feb 08 05:56:43 EET 2007
 */

class Ecom_Core_Block_Flush extends Ecom_Core_Block_Abstract
{
	function toHtml()
	{
	    ob_implicit_flush();
	    
	    $list = $this->getAttribute('sortedChildrenList');
	    if (!empty($list)) {
    	    foreach ($list as $name) {
    	        $block = Ecom::getBlock($name);
    	        if (!$block) {
    	            Ecom::exception('Invalid block: '.$name);
    	        }
    	        echo $block->toHtml();
    	    }
	    }
	}
}// Class Ecom_Core_Block_List END