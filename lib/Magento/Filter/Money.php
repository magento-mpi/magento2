<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Filter
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Filter;

class Money implements \Zend_Filter_Interface
{
    /**
     * @var string
     */
    protected $_format;

    /**
     * @param string $format
     */
    public function __construct($format)
    {
        $this->_format = $format;
    }

    /**
     * @param float $value
     * @return string
     */
    public function filter($value)
    {
        return money_format($this->_format, $value);
    }
}
