<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
/**
 * Locale currency source
 */
class Magento_Backend_Model_Config_Source_Locale_Currency implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var array
     */
    protected $_option;

    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @param Magento_Core_Model_LocaleInterface $locale
     */
    public function __construct(Magento_Core_Model_LocaleInterface $locale)
    {
        $this->_locale = $locale;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_locale->getOptionCurrencies();
    }
}
