<?php



/**
 * Base html block
 *
 * @copyright  Varien, 2007
 * @version    1.0 
 * @author     Moshe Gurvich <moshe@varien.com>
 * @author	   Soroka Dmitriy <dmitriy@varien.com>
 * @date       Thu Feb 08 05:56:43 EET 2007
 */

class Mage_Core_Block_Text extends Mage_Core_Block_Abstract
{
    function setText($text)
    {
        $this->setAttribute('text', $text);
        return $this;
    }
    
    function getText()
    {
        return $this->getAttribute('text');
    }
    
    function addText($text, $before=false)
    {
        if ($before) {
            $this->setText($text.$this->getText());
        } else {
            $this->setText($this->getText().$text);
        }
    }
    
	function toHtml()
	{
    	return $this->getText();
	}
}// Class Mage_Core_Block_List END