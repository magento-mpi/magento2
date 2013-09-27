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

class Magento_Sales_Model_Config_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_readerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    protected function setUp()
    {
        $this->_readerMock = $this->getMockBuilder('Magento_Sales_Model_Config_Reader')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_cacheMock = $this->getMockBuilder('Magento_Core_Model_Cache_Type_Config')
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
        $configData = new Magento_Sales_Model_Config_Data($this->_readerMock, $this->_cacheMock);

        $this->assertEquals($expected, $configData->get());
    }
}