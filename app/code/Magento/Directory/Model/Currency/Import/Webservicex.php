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
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     */
    public function __construct(
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
    ) {
        parent::__construct($currencyFactory);
        $this->_storeConfig = $coreStoreConfig;
        $this->_httpClient = new \Magento\HTTP\ZendClient();
    }

    /**
     * @param string $currencyFrom
     * @param string $currencyTo
     * @param int $retry
     * @return float|null
     */
    protected function _convert($currencyFrom, $currencyTo, $retry = 0)
    {
        $url = str_replace('{{CURRENCY_FROM}}', $currencyFrom, $this->_url);
        $url = str_replace('{{CURRENCY_TO}}', $currencyTo, $url);

        try {
            $response = $this->_httpClient->setUri(
                $url
            )->setConfig(
                array(
                    'timeout' => $this->_storeConfig->getValue(
                        'currency/webservicex/timeout',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    )
                )
            )->request(
                'GET'
            )->getBody();

            $xml = simplexml_load_string($response, null, LIBXML_NOERROR);
            if (!$xml) {
                $this->_messages[] = __('We can\'t retrieve a rate from %1.', $url);
                return null;
            }
            return (double)$xml;
        } catch (\Exception $e) {
            if ($retry == 0) {
                $this->_convert($currencyFrom, $currencyTo, 1);
            } else {
                $this->_messages[] = __('We can\'t retrieve a rate from %1.', $url);
            }
        }
    }
}
