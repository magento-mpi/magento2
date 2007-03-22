<?php



/**
 * Base html block
 *
 * @copyright  Varien, 2007
 * @version    1.0 
 * @author	   Soroka Dmitriy <dmitriy@varien.com>
 * @date       Thu Feb 08 05:56:43 EET 2007
 */

class Mage_Core_Block_Text_List extends Mage_Core_Block_Text 
{
	function toString()
	{
	    $this->setText('');
	    $list = $this->getAttribute('sortedChildrenList');
	    if (!empty($list)) {
    	    foreach ($list as $name) {
    	        $block = Mage::getBlock($name);
    	        if (!$block) {
    	            Mage::exception('Invalid block: '.$name);
    	        }
    	        $this->addText($block->toString());
    	    }
	    }
	    return parent::toString();
	}
}// Class Mage_Core_Block_List END