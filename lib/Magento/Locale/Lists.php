<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Locale;

class Lists implements \Magento\Locale\ListsInterface
{
    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Locale\ConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\AppInterface
     */
    protected $_app;

    /**
     * @var \Magento\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @var string
     */
    protected $_currencyInstalled;

    /**
     * @param \Magento\App\State $appState
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Locale\ConfigInterface $config
     * @param \Magento\AppInterface $app
     * @param \Magento\Locale\ResolverInterface $localeResolver
     * @param string $currencyInstalled
     * @param string $locale
     */
    public function __construct(
        \Magento\App\State $appState,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Locale\ConfigInterface $config,
        \Magento\AppInterface $app,
        \Magento\Locale\ResolverInterface $localeResolver,
        $currencyInstalled,
        $locale = null
    ) {
        $this->_appState = $appState;
        $this->_storeManager = $storeManager;
        $this->_config = $config;
        $this->_app = $app;
        $this->_localeResolver = $localeResolver;
        $this->_localeResolver->setLocale($locale);
        $this->_currencyInstalled = $currencyInstalled;
    }

    /**
     * @inheritdoc
     */
    public function getOptionLocales()
    {
        return $this->_getOptionLocales();
    }

    /**
     * @inheritdoc
     */
    public function getTranslatedOptionLocales()
    {
        return $this->_getOptionLocales(true);
    }

    /**
     * Get options array for locale dropdown
     *
     * @param   bool $translatedName translation flag
     * @return  array
     */
    protected function _getOptionLocales($translatedName = false)
    {
        $options = array();
        $locales = $this->_localeResolver->getLocale()->getLocaleList();
        $languages = $this->_localeResolver->getLocale()->getTranslationList(
            'language', $this->_localeResolver->getLocale()
        );
        $countries = $this->_localeResolver->getLocale()
            ->getTranslationList('territory', $this->_localeResolver->getLocale(), 2);

        $allowed = $this->_config->getAllowedLocales();
        foreach (array_keys($locales) as $code) {
            if (strstr($code, '_')) {
                if (!in_array($code, $allowed)) {
                    continue;
                }
                $data = explode('_', $code);
                if (!isset($languages[$data[0]]) || !isset($countries[$data[1]])) {
                    continue;
                }
                if ($translatedName) {
                    $label = ucwords($this->_localeResolver->getLocale()->getTranslation($data[0], 'language', $code))
                        . ' ('
                        . $this->_localeResolver->getLocale()->getTranslation($data[1], 'country', $code)
                        . ') / '
                        . $languages[$data[0]] . ' (' . $countries[$data[1]] . ')';
                } else {
                    $label = $languages[$data[0]] . ' (' . $countries[$data[1]] . ')';
                }
                $options[] = array(
                    'value' => $code,
                    'label' => $label
                );
            }
        }
        return $this->_sortOptionArray($options);
    }

    /**
     * @inheritdoc
     */
    public function getOptionTimezones()
    {
        $options= array();
        $zones  = $this->getTranslationList('windowstotimezone');
        ksort($zones);
        foreach ($zones as $code => $name) {
            $name = trim($name);
            $options[] = array(
                'label' => empty($name) ? $code : $name . ' (' . $code . ')',
                'value' => $code,
            );
        }
        return $this->_sortOptionArray($options);
    }

    /**
     * @inheritdoc
     */
    public function getOptionWeekdays($preserveCodes = false, $ucFirstCode = false)
    {
        $options= array();
        $days = $this->getTranslationList('days');
        $days = $preserveCodes ? $days['format']['wide']  : array_values($days['format']['wide']);
        foreach ($days as $code => $name) {
            $options[] = array(
                'label' => $name,
                'value' => $ucFirstCode ? ucfirst($code) : $code,
            );
        }
        return $options;
    }

    /**
     * @inheritdoc
     */
    public function getOptionCountries()
    {
        $options    = array();
        $countries  = $this->getCountryTranslationList();

        foreach ($countries as $code=>$name) {
            $options[] = array(
                'label' => $name,
                'value' => $code,
            );
        }
        return $this->_sortOptionArray($options);
    }

    /**
     * @inheritdoc
     */
    public function getOptionCurrencies()
    {
        $currencies = $this->getTranslationList('currencytoname');
        $options = array();
        $allowed = $this->_getAllowedCurrencies();

        foreach ($currencies as $name => $code) {
            if (!in_array($code, $allowed)) {
                continue;
            }

            $options[] = array(
                'label' => $name,
                'value' => $code,
            );
        }
        return $this->_sortOptionArray($options);
    }

    /**
     * Retrive array of allowed currencies
     *
     * @return array
     */
    protected function _getAllowedCurrencies()
    {
        if ($this->_appState->isInstalled()) {
            $allowed = explode(',', $this->_storeManager->getStore()
                ->getConfig($this->_currencyInstalled)
            );
        } else {
            $allowed = $this->_config->getAllowedCurrencies();
        }
        return $allowed;
    }

    /**
     * @inheritdoc
     */
    public function getOptionAllCurrencies()
    {
        $currencies = $this->getTranslationList('currencytoname');
        $options = array();
        foreach ($currencies as $name=>$code) {
            $options[] = array(
                'label' => $name,
                'value' => $code,
            );
        }
        return $this->_sortOptionArray($options);
    }

    /**
     * @param array $option
     * @return array
     */
    protected function _sortOptionArray($option)
    {
        $data = array();
        foreach ($option as $item) {
            $data[$item['value']] = $item['label'];
        }
        asort($data);
        $option = array();
        foreach ($data as $key => $label) {
            $option[] = array(
                'value' => $key,
                'label' => $label
            );
        }
        return $option;
    }

    /**
     * @inheritdoc
     */
    public function getTranslationList($path = null, $value = null)
    {
        return $this->_localeResolver->getLocale()
            ->getTranslationList($path, $this->_localeResolver->getLocale(), $value);
    }

    /**
     * @inheritdoc
     */
    public function getCountryTranslation($value)
    {
        return $this->_localeResolver->getLocale()
            ->getTranslation($value, 'country', $this->_localeResolver->getLocale());
    }
}
