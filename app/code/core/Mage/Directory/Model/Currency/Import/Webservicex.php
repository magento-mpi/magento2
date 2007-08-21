<?php
/**
 * Currency rate import model (From www.webservicex.net)
 *
 * @package     Mage
 * @subpackage  Directory
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Directory_Model_Currency_Import_Webservicex extends Mage_Directory_Model_Currency_Import_Abstract
{
    const CONVERTOR_URL = 'http://www.webservicex.net/CurrencyConvertor.asmx/ConversionRate?FromCurrency=string&ToCurrency=string';
    
    protected function _convert($currencyFrom, $currencyTo)
    {
        
    }
}
