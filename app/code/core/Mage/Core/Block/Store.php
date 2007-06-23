<?php
/**
 * Core store block
 *
 * @package     Mage
 * @subpackage  Core
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Block_Store extends Mage_Core_Block_Template 
{
    public function __construct() 
    {
        $this->setTemplate('core/store.phtml');
        $website = Mage::getSingleton('core/website');
        $storeCodes = $website->getStoreCodes();
        
        $arrLanguages = array();
        foreach ($storeCodes as $storeCode) {
            if ($storeCode!='admin') {
                $storeConfig = Mage::getConfig()->getStoreConfig($storeCode);
                $language = (string)$storeConfig->language;
            	if (Mage::getSingleton('core/store')->getLanguageCode() != $language) {
            	    $store = Mage::getModel('core/store')->setCode($storeCode);
            	    $arrLanguages[$language] = $store->getUrl(array());
            	}
            	else {
            	    $arrLanguages[$language] = false;
            	}
            }
        }
        $this->assign('languages', $arrLanguages);
    }
}
