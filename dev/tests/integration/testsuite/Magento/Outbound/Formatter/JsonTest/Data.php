<?php
/**
 * Class Data is a simple class to test converting a PHP class into JSON data
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Outbound
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Outbound\Formatter\JsonTest;

class Data
{
    public $dataA;

    protected $_dataB;

    public function __construct($first, $second)
    {
        $this->dataA = $first;
        $this->_dataB = $second;
    }

    public function getB()
    {
        return $this->_dataB;
    }
}
