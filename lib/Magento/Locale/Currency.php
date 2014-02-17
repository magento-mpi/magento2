<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Locale;

class Currency implements \Magento\Locale\CurrencyInterface
{
    /**
     * @var array
     */
    protected static $_currencyCache = array();

    /**
     * Core event manager proxy
     *
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * @var \Magento\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @var \Magento\CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param ResolverInterface $localeResolver
     * @param \Magento\CurrencyFactory $currencyFactory
     */
    public function __construct(
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Locale\ResolverInterface $localeResolver,
        \Magento\CurrencyFactory $currencyFactory
    ) {
        $this->_eventManager = $eventManager;
        $this->_localeResolver = $localeResolver;
        $this->_currencyFactory = $currencyFactory;
    }

    /**
     * Retrieve currency code
     *
     * @return string
     */
    public function getDefaultCurrency()
    {
        return \Magento\Locale\CurrencyInterface::DEFAULT_CURRENCY;
    }

    /**
     * Create \Zend_Currency object for current locale
     *
     * @param   string $currency
     * @return  \Magento\Currency
     */
    public function getCurrency($currency)
    {
        \Magento\Profiler::start('locale/currency');
        if (!isset(self::$_currencyCache[$this->_localeResolver->getLocaleCode()][$currency])) {
            $options = array();
            try {
                $currencyObject = $this->_currencyFactory->create(array(
                    'options' => $currency,
                    'locale' => $this->_localeResolver->getLocale(),
                ));
            } catch (\Exception $e) {
                $currencyObject = $this->_currencyFactory->create(array(
                    'options' => $this->getDefaultCurrency(),
                    'locale' => $this->_localeResolver->getLocale(),
                ));
                $options['name'] = $currency;
                $options['currency'] = $currency;
                $options['symbol'] = $currency;
            }

            $options = new \Magento\Object($options);
            $this->_eventManager->dispatch('currency_display_options_forming', array(
                'currency_options' => $options,
                'base_code' => $currency
            ));

            $currencyObject->setFormat($options->toArray());
            self::$_currencyCache[$this->_localeResolver->getLocaleCode()][$currency] = $currencyObject;
        }
        \Magento\Profiler::stop('locale/currency');
        return self::$_currencyCache[$this->_localeResolver->getLocaleCode()][$currency];
    }
}
