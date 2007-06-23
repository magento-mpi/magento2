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
        $stores = Mage::getConfig()->getNode('global/stores')->asArray();
        
        $arrLanguages = array();
        foreach ($stores as $storeCode => $storeInfo) {
            if ($storeCode!='admin') {
            	if (Mage::getSingleton('core/store')->getLanguage() != $storeInfo['language']) {
            	    $store = Mage::getModel('core/store')->setCode($storeCode);
            	    $arrLanguages[$storeInfo['language']] = $store->getUrl(array());
            	}
            	else {
            	    $arrLanguages[$storeInfo['language']] = false;
            	}
            }
        }
        $this->assign('languages', $arrLanguages);
    }
}
