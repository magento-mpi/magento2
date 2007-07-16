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
        Mage::log('Directory observer: action predispatch event');
        $code = Mage::getSingleton('core/store')->getDefaultCurrencyCode();
        if ($code) {
            $currency = Mage::getModel('directory/currency')->load($code);
            Mage::getSingleton('core/store')->setDefaultCurrency($currency);
        }
        
        if ($observer->getEvent()->getControllerAction()->getRequest()->getParam('currency')) {
            Mage::getSingleton('core/store')->setCurrentCurrencyCode(
                $observer->getEvent()->getControllerAction()->getRequest()->getParam('currency')
            );
        }
        
        $code = Mage::getSingleton('core/store')->getCurrentCurrencyCode();
        if ($code) {
            $currency = Mage::getModel('directory/currency')->load($code);
            Mage::getSingleton('core/store')->setCurrentCurrency($currency);
        }
        
    }
    
    /**
     * Before save quote event observer method
     *
     * @param Varien_Event_Observer $observer
     */
    public function beforeSaveQuote($observer)
    {
        Mage::log('Directory observer: before save quote event');
        $quote = $observer->getEvent()->getQuote();
        if ($quote instanceof Varien_Object) {
            $baseCurrency = (string)Mage::getConfig()->getNode('global/default/currency');
            $defaultCurrency = Mage::getSingleton('core/store')->getDefaultCurrencyCode();
            $currentCurrency = Mage::getSingleton('core/store')->getCurrentCurrencyCode();
            
            $currency = Mage::getModel('directory/currency');
            
            $quote->setBaseCurrencyCode($baseCurrency);
            $quote->setStoreCurrencyCode($defaultCurrency);
            $quote->setCurrentCurrencyCode($currentCurrency);
            $quote->setStoreToBaseCurrencyRate($currency->getResource()->getRate($defaultCurrency, $baseCurrency));
            $quote->setStoreToCurrentCurrencyRate($currency->getResource()->getRate($defaultCurrency, $currentCurrency));
        }
    }
    
    /**
     * Before save order event observer method
     *
     * @param Varien_Event_Observer $observer
     */
    public function beforeSaveOrder($observer)
    {
        Mage::log('Directory observer: before save order event');

        $order = $observer->getEvent()->getOrder();
        if ($order instanceof Varien_Object) {
            $baseCurrency = (string)Mage::getConfig()->getNode('global/default/currency');
            $defaultCurrency = Mage::getSingleton('core/store')->getDefaultCurrencyCode();
            $currentCurrency = Mage::getSingleton('core/store')->getCurrentCurrencyCode();
            
            $currency = Mage::getModel('directory/currency');
            
            $order->setBaseCurrencyCode($baseCurrency);
            $order->setStoreCurrencyCode($defaultCurrency);
            $order->setCurrentCurrencyCode($currentCurrency);
            $order->setStoreToBaseCurrencyRate($currency->getResource()->getRate($defaultCurrency, $baseCurrency));
            $order->setStoreToCurrentCurrencyRate($currency->getResource()->getRate($defaultCurrency, $currentCurrency));
        }
    }
}
