<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Helper;

class StringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Helper\String
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Helper\String');
    }

    /**
     * @covers \Magento\Core\Helper\String::splitInjection
     * @covers \Magento\Core\Helper\String::strrev
     */
    public function testSplitInjection()
    {
        $string = '1234567890';
        $this->assertEquals('1234 5678 90', $this->_helper->splitInjection($string, 4));
    }
}
