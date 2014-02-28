<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Config\Source;

class Currency implements \Magento\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $_options;

    /**
     * @var \Magento\LocaleInterface
     */
    protected $_locale;

    /**
     * @param \Magento\LocaleInterface $locale
     */
    public function __construct(\Magento\LocaleInterface $locale)
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
