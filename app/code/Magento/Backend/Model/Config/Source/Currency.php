<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_Source_Currency implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var array
     */
    protected $_options;

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
        if (!$this->_options) {
            $this->_options = $this->_locale->getOptionCurrencies();
        }
        $options = $this->_options;
        return $options;
    }
}
