<?php
/**
 * Currency dropdown block
 *
 * @package     Mage
 * @subpackage  Directory
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Directory_Block_Currency extends Mage_Core_Block_Template
{
    public function __construct($data=array()) 
    {
        parent::__construct($data);
        
        try {
            $currencies = Mage::getResourceModel('directory/currency_collection')
                ->addLanguageFilter()
                ->addCodeFilter(Mage::getSingleton('core/store')->getAvailableCurrencyCodes())
                ->load();
        }
        catch (Exception $e){
            $currencies = array();
        }
            
        $this->assign('currencies', $currencies);
        $this->assign('currentCurrencyCode', Mage::getSingleton('core/store')->getCurrentCurrencyCode());
    }
    
    public function getCurrencyCount()
    {
        return $this->getCurrencies()->getSize();
    }
    
    public function getCurrencies()
    {
        $collection = $this->getData('currencies');
        if (is_null($collection)) {
            $collection =  Mage::getResourceModel('directory/currency_collection')
                ->addLanguageFilter()
                ->addCodeFilter(Mage::getSingleton('core/store')->getAvailableCurrencyCodes())
                ->load();
            $this->setData('currencies', $collection);
        }
        return $collection;
    }
    
    public function getSwitchUrl()
    {
        return $this->getUrl('directory/currency/switch');
    }
    
    public function getCurrentCurrencyCode()
    {
        $code = $this->getData('current_currency_code');
        if (is_null($code)) {
            $code = Mage::getSingleton('core/store')->getCurrentCurrencyCode();
            $this->setData('current_currency_code', $code);
        }
        return $code;
    }
}
