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

class SchemaLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleReaderMock;

    /**
     * @var \Magento\Sales\Model\Config\SchemaLocator
     */
    protected $_locator;

    /**
     * Initialize parameters
     */
    protected function setUp()
    {
        $this->_moduleReaderMock = $this->getMockBuilder('Magento\Module\Dir\Reader')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_moduleReaderMock->expects($this->once())
            ->method('getModuleDir')->with('etc', 'Magento_Sales')->will($this->returnValue('schema_dir'));
        $this->_locator = new \Magento\Sales\Model\Config\SchemaLocator($this->_moduleReaderMock);
    }

    /**
     * Testing that schema has file
     */
    public function testGetSchema()
    {
        $this->assertEquals('schema_dir/sales.xsd', $this->_locator->getSchema());
    }
}
