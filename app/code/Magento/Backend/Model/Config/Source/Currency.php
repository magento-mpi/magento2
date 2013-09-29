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

class Currency implements \Magento\Core\Model\Option\ArrayInterface
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = \Mage::app()->getLocale()->getOptionCurrencies();
        }
        $options = $this->_options;
        return $options;
    }
}
