<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Currency rate import model (From www.webservicex.net)
 */
namespace Magento\Directory\Model\Currency\Import;

class Webservicex extends \Magento\Directory\Model\Currency\Import\AbstractImport
{
    /**
     * @var string
     */
    protected $_url = 'http://www.webservicex.net/CurrencyConvertor.asmx/ConversionRate?FromCurrency={{CURRENCY_FROM}}&ToCurrency={{CURRENCY_TO}}';

     /**
     * HTTP client
     *
     * @var \Magento\HTTP\ZendClient
     */
    protected $_httpClient;

    /**
     * Core store config
     *
     * @var \Magento\Store\Model\Config
     */
    protected $_coreStoreConfig;

    /**
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Store\Model\Config $coreStoreConfig
     */
    public function __construct(
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Store\Model\Config $coreStoreConfig
    ) {
        parent::__construct($currencyFactory);
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_httpClient = new \Magento\HTTP\ZendClient();
    }

    /**
     * @param string $currencyFrom
     * @param string $currencyTo
     * @param int $retry
     * @return float|null
     */
    protected function _convert($currencyFrom, $currencyTo, $retry=0)
    {
        $url = str_replace('{{CURRENCY_FROM}}', $currencyFrom, $this->_url);
        $url = str_replace('{{CURRENCY_TO}}', $currencyTo, $url);

        try {
            $response = $this->_httpClient
                ->setUri($url)
                ->setConfig(array('timeout' => $this->_coreStoreConfig->getValue('currency/webservicex/timeout'), \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE))
                ->request('GET')
                ->getBody();

            $xml = simplexml_load_string($response, null, LIBXML_NOERROR);
            if( !$xml ) {
                $this->_messages[] = __('We can\'t retrieve a rate from %1.', $url);
                return null;
            }
            return (float) $xml;
        }
        catch (\Exception $e) {
            if( $retry == 0 ) {
                $this->_convert($currencyFrom, $currencyTo, 1);
            } else {
                $this->_messages[] = __('We can\'t retrieve a rate from %1.', $url);
            }
        }
    }
}
