<?php
/**
 * Abstract model for import currency
 *
 * @package     Mage
 * @subpackage  Directory
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
abstract class Mage_Directory_Model_Currency_Import_Abstract
{
    protected function _getCurrencyCodes()
    {
        $collection = Mage::getResourceModel('directory/currency_collection')
            ->addLanguageFilter()
            ->load();
        $codes = array();
        foreach ($collection as $currency) {
        	$codes[] = $currency->getCode();
        }
        return $codes;
    }
    
    abstract protected function _convert($currencyFrom, $currencyTo);
    
    protected function _saveRates($rates)
    {
        foreach ($rates as $currencyCode => $currencyRates) {
        	Mage::getModel('directory/currency')
                ->setId($currencyCode)
                ->setRates($currencyRates)
                ->save();
        }
        return $this;
    }
    
    public function importRates()
    {
        $data = array();
        $currencies = $this->_getCurrencyCodes();
        set_time_limit(0);
        foreach ($currencies as $currencyFrom) {
            if (!isset($data[$currencyFrom])) {
                $data[$currencyFrom] = array();
            }
            
        	foreach ($currencies as $currencyTo) {
        	    if ($currencyFrom == $currencyTo) {
        	        $data[$currencyFrom][$currencyTo] = 1;
        	    }
        		else {
        		    $data[$currencyFrom][$currencyTo] = $this->_convert($currencyFrom, $currencyTo);
        		}
        	}
        }
        $this->_saveRates($data);
        return $this;
    }
}
