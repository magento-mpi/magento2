<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract utility class
 */
class Magento_Test_Utility_Abstract
{
    /**
     * @var PHPUnit_Framework_TestCase
     */
    protected $_testCase;

    public function __construct(PHPUnit_Framework_TestCase $testCase)
    {
        $this->_testCase = $testCase;
    }
}
