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
        $code = Mage::getSingleton('core/website')->getDefaultCurrencyCode();
        if ($code) {
            Mage::getSingleton('core/website')->setDefaultCurrency(Mage::getModel('directory/currency')->load($code));
        }
        
        if ($observer->getEvent()->getControllerAction()->getRequest()->getParam('currency')) {
            Mage::getSingleton('core/website')->setCurrentCurrencyCode(
                $observer->getEvent()->getControllerAction()->getRequest()->getParam('currency')
            );
        }
        
        $code = Mage::getSingleton('core/website')->getCurrentCurrencyCode();
        if ($code) {
            Mage::getSingleton('core/website')->setCurrentCurrency(Mage::getModel('directory/currency')->load($code));
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
            $defaultCurrency = Mage::getSingleton('core/website')->getDefaultCurrencyCode();
            $currentCurrency = Mage::getSingleton('core/website')->getCurrentCurrencyCode();
            
            $currency = Mage::getModel('directory/currency');
            
            $quote->setBaseCurrencyCode($baseCurrency);
            $quote->setWebsiteCurrencyCode($defaultCurrency);
            $quote->setCurrentCurrencyCode($currentCurrency);
            $quote->setWebsiteToBaseCurrencyRate($currency->getResource()->getRate($defaultCurrency, $baseCurrency));
            $quote->setWebsiteToCurrentCurrencyRate($currency->getResource()->getRate($defaultCurrency, $currentCurrency));
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
            $defaultCurrency = Mage::getSingleton('core/website')->getDefaultCurrencyCode();
            $currentCurrency = Mage::getSingleton('core/website')->getCurrentCurrencyCode();
            
            $currency = Mage::getModel('directory/currency');
            
            $order->setBaseCurrencyCode($baseCurrency);
            $order->setWebsiteCurrencyCode($defaultCurrency);
            $order->setCurrentCurrencyCode($currentCurrency);
            $order->setWebsiteToBaseCurrencyRate($currency->getResource()->getRate($defaultCurrency, $baseCurrency));
            $order->setWebsiteToCurrentCurrencyRate($currency->getResource()->getRate($defaultCurrency, $currentCurrency));
        }
    }
}
