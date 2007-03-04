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

class Ecom_Core_Block_Text_Tag extends Ecom_Core_Block_Text
{
	function __construct()
	{
		parent::__construct();
		
		$this->setAttribute('tagParams', array());
	}

	function setTagName($name)
	{
	    $this->setAttribute('tagName', $name);
	    return $this;
	}

	function setTagParam($param, $value=null)
	{
	    if (is_array($param) && is_null($value)) {
	        foreach ($param as $k=>$v) {
	            $this->setTagParam($k, $v);
	        }
	    } else {
	        $params = $this->getAttribute('tagParams');
	        $params[$param] = $value;
	        $this->setAttribute('tagParams', $params);
	    }
	    return $this;
	}

	function setContents($text)
	{
	    $this->setAttribute('tagContents', $text);
	    return $this;
	}

	function toHtml()
	{
	    $this->setText('<'.$this->getAttribute('tagName').' ');
	    if ($this->getAttribute('tagParams')) {
    	    foreach ($this->getAttribute('tagParams') as $k=>$v) {
    	        $this->addText($k.'="'.$v.'" ');
    	    }
	    }

        $this->addText('>'.$this->getAttribute('tagContents').'</'.$this->getAttribute('tagName').'>'."\r\n");
	    return parent::toHtml();
	}
}// Class Ecom_Core_Block_List END