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
    const CONVERTOR_URL = 'http://www.webservicex.net/CurrencyConvertor.asmx/ConversionRate?FromCurrency={{CURRENCY_FROM}}&ToCurrency={{CURRENCY_TO}}';
    
    /**
     * HTTP client
     *
     * @var Varien_Http_Client
     */
    protected $_httpClient;
    
    public function __construct()
    {
        $this->_httpClient = new Varien_Http_Client();
    }
    
    protected function _convert($currencyFrom, $currencyTo)
    {
        $url = str_replace('{{CURRENCY_FROM}}', $currencyFrom, self::CONVERTOR_URL);
        $url = str_replace('{{CURRENCY_TO}}', $currencyTo, $url);
        
        try {
            $response = $this->_httpClient
                ->setUri($url)
                ->request('GET')
                ->getBody();
                
            $xml = simplexml_load_string($response);
            return (float) $xml;
        }
        catch (Exception $e) {
            Mage::throwException('Can not retrieve rate from ' . $url);
        }
    }
}
