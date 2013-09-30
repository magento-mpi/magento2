<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_Source_Locale_Currency_All implements Magento_Core_Model_Option_ArrayInterface
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
     * @param bool $isMultiselect
     * @return array
     */
    public function toOptionArray($isMultiselect = false)
    {
        if (!$this->_options) {
            $this->_options = $this->_locale->getOptionAllCurrencies();
        }
        $options = $this->_options;
        if (!$isMultiselect) {
            array_unshift($options, array('value'=>'', 'label'=>''));
        }

        return $options;
    }
}
