<?php
/**
 * Base html block
 *
 * @copyright  Varien, 2007
 * @version    1.0 
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */

class Mage_Core_Block_Text_Tag_Css_Admin extends Mage_Core_Block_Text_Tag_Css
{
    function __construct()
    {
        parent::__construct();
        $theme = empty($_COOKIE['admtheme']) ? 'default' : $_COOKIE['admtheme'];
        $this->setAttribute('theme', $theme);
    }

    function setHref($href, $type='skin')
    {
        $url = Mage::getBaseUrl(array('_type'=>$type)).$href.$this->getAttribute('theme').'.css';
        return $this->setTagParam('href', $url);
    }
    
}// Class Mage_Core_Block_List END