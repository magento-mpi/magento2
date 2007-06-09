<?php
/**
 * Core website block
 *
 * @package     Mage
 * @subpackage  Core
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Block_Website extends Mage_Core_Block_Template 
{
    public function __construct() 
    {
        $this->setTemplate('core/website.phtml');
        $websites = Mage::getConfig()->getNode('global/websites')->asArray();
        
        $arrLanguages = array();
        foreach ($websites as $websiteCode => $websiteInfo) {
            if ($websiteCode!='admin') {
            	if (Mage::registry('website')->getLanguage() != $websiteInfo['language']) {
            	    $website = Mage::getModel('core/website')->setCode($websiteCode);
            	    $arrLanguages[$websiteInfo['language']] = $website->getUrl(array());
            	}
            	else {
            	    $arrLanguages[$websiteInfo['language']] = false;
            	}
            }
        }
        $this->assign('languages', $arrLanguages);
    }
}
