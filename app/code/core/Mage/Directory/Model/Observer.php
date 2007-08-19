<?php
/**
 * Directory module events observer
 *
 * @package     Mage
 * @subpackage  Directory
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Directory_Model_Observer
{
    /**
     * Action preDispatch event observet method
     *
     * @param Varien_Event_Observer $observer
     */
    public function actionPreDispatch($observer)
    {
        $code = Mage::getSingleton('core/store')->getDefaultCurrencyCode();
        if ($code) {
            $currency = Mage::getModel('directory/currency')->load($code);
            Mage::getSingleton('core/store')->setDefaultCurrency($currency);
        }
        
        if ($newCode = $observer->getEvent()->getControllerAction()->getRequest()->getParam('currency')) {
            Mage::getSingleton('core/store')->setCurrentCurrencyCode($newCode);
        }
        
        $code = Mage::getSingleton('core/store')->getCurrentCurrencyCode();
        if ($code) {
            $currency = Mage::getModel('directory/currency')->load($code);
            Mage::getSingleton('core/store')->setCurrentCurrency($currency);
        }
        
    }
}
