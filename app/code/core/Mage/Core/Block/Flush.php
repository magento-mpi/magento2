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

class Mage_Core_Block_Flush extends Mage_Core_Block_Abstract
{
	function toString()
	{
	    ob_implicit_flush();
	    
	    $list = $this->getAttribute('sortedChildrenList');
	    if (!empty($list)) {
    	    foreach ($list as $name) {
    	        $block = Mage::getBlock($name);
    	        if (!$block) {
    	            Mage::exception('Invalid block: '.$name);
    	        }
    	        echo $block->toString();
    	    }
	    }
	}
}// Class Mage_Core_Block_List END