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
namespace Magento\Backend\Model\Config\Source\Locale;

class Currency implements \Magento\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $_option;

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
        return $this->_locale->getOptionCurrencies();
    }
}
