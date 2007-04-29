<?php



/**
 * Base html block
 *
 * @copyright  Varien, 2007
 * @version    1.0
 * @author	   Soroka Dmitriy <dmitriy@varien.com>
 * @date       Thu Feb 08 05:56:43 EET 2007
 */

class Mage_Core_Block_Text_Tag extends Mage_Core_Block_Text
{
	function __construct()
	{
		parent::__construct();
		
		$this->setTagParams(array());
	}

	function setTagParam($param, $value=null)
	{
	    if (is_array($param) && is_null($value)) {
	        foreach ($param as $k=>$v) {
	            $this->setTagParam($k, $v);
	        }
	    } else {
	        $params = $this->getTagParams();
	        $params[$param] = $value;
	        $this->setTagParams($params);
	    }
	    return $this;
	}

	function setContents($text)
	{
	    $this->setTagContents($text);
	    return $this;
	}

	function toHtml()
	{
	    $this->setText('<'.$this->getTagName().' ');
	    if ($this->getTagParams()) {
    	    foreach ($this->getTagParams() as $k=>$v) {
    	        $this->addText($k.'="'.$v.'" ');
    	    }
	    }

        $this->addText('>'.$this->getTagContents().'</'.$this->getTagName().'>'."\r\n");
	    return parent::toHtml();
	}
}// Class Mage_Core_Block_List END