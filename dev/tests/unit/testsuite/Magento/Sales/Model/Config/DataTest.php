<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Config;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_readerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    protected function setUp()
    {
        $this->_readerMock = $this->getMockBuilder('Magento\Sales\Model\Config\Reader')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_cacheMock = $this->getMockBuilder('Magento\Core\Model\Cache\Type\Config')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testGet()
    {
        $expected = array(
            'someData' => array(
                'someValue',
                'someKey' => 'someValue'
            )
        );
        $this->_cacheMock->expects($this->any())->method('load')->will($this->returnValue(serialize($expected)));
        $configData = new \Magento\Sales\Model\Config\Data($this->_readerMock, $this->_cacheMock);

        $this->assertEquals($expected, $configData->get());
    }
}
