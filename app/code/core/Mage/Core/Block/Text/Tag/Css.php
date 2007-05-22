<?php



/**
 * Base html block
 *
 * @copyright  Varien, 2007
 * @version    1.0 
 * @author     Moshe Gurvich <moshe@varien.com>
 * @date       Thu Feb 08 05:56:43 EET 2007
 */

class Mage_Core_Block_Text_Tag_Css extends Mage_Core_Block_Text_Tag
{
    function __construct()
    {
        parent::__construct();

        $this->setTagName('link');
        $this->setTagParams(array('rel'=>'stylesheet', 'type'=>'text/css', 'media'=>'all'));
    }
    
    function setHref($href, $type=null)
    {
        $type = (string)$type;
        if (empty($type)) { 
            $type = 'skin'; 
        }
        $url = Mage::getBaseUrl(array('_type'=>$type)).$href;
        
        return $this->setTagParam('href', $url);
    }
}// Class Mage_Core_Block_List END