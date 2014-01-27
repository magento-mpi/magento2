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

class Sprintf implements \Zend_Filter_Interface
{
    /**
     * @var string
     */
    protected $_format;

    /**
     * @var int
     */
    protected $_decimals;

    /**
     * @var string
     */
    protected $_decPoint;

    /**
     * @var string
     */
    protected $_thousandsSep;

    /**
     * @param string $format
     * @param null|int $decimals
     * @param string $decPoint
     * @param string $thousandsSep
     */
    public function __construct($format, $decimals = 0, $decPoint = '.', $thousandsSep = ',')
    {
        $this->_format = $format;
        $this->_decimals = $decimals;
        $this->_decPoint = $decPoint;
        $this->_thousandsSep = $thousandsSep;
    }

    /**
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        if (!is_null($this->_decimals)) {
            $value = number_format($value, $this->_decimals, $this->_decPoint, $this->_thousandsSep);
        }
        $value = sprintf($this->_format, $value);
        return $value;
    }
}
