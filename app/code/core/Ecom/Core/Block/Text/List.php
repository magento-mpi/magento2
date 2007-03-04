<?php

#include_once 'Ecom/Core/Block/Text.php';

/**
 * Base html block
 *
 * @copyright  Varien, 2007
 * @version    1.0 
 * @author	   Soroka Dmitriy <dmitriy@varien.com>
 * @date       Thu Feb 08 05:56:43 EET 2007
 */

class Ecom_Core_Block_Text_List extends Ecom_Core_Block_Text 
{
	function toHtml()
	{
	    $list = $this->getAttribute('sortedChildrenList');
	    if (!empty($list)) {
    	    foreach ($list as $name) {
    	        $block = Ecom::getBlock($name);
    	        if (!$block) {
    	            Ecom::exception('Invalid block: '.$name);
    	        }
    	        $this->addText($block->toHtml());
    	    }
	    }
	    return parent::toHtml();
	}
}// Class Ecom_Core_Block_List END