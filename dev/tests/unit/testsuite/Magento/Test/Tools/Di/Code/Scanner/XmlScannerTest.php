<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Test\Tools\Di\Code\Scanner;

class XmlScannerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\Di\Code\Scanner\XmlScanner
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_testDir;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_logMock;

    /**
     * @var array
     */
    protected $_testFiles = array();

    protected function setUp()
    {
        $this->_model = new \Magento\Tools\Di\Code\Scanner\XmlScanner(
            $this->_logMock = $this->getMock('\Magento\Tools\Di\Compiler\Log\Log', array(), array(), '', false)
        );
        $this->_testDir = str_replace('\\', '/', realpath(__DIR__ . '/../../') . '/_files');
        $this->_testFiles = array(
            $this->_testDir . '/app/code/Magento/SomeModule/etc/adminhtml/system.xml',
            $this->_testDir . '/app/code/Magento/SomeModule/etc/di.xml',
            $this->_testDir . '/app/code/Magento/SomeModule/view/frontend/default.xml'
        );
    }

    public function testCollectEntities()
    {
        $className = 'Magento\Core\Model\Config\Invalidator\Proxy';
        $this->_logMock->expects(
            $this->at(0)
        )->method(
            'add'
        )->with(
            4,
            $className,
            'Invalid proxy class for ' . substr($className, 0, -5)
        );
        $this->_logMock->expects(
            $this->at(1)
        )->method(
            'add'
        )->with(
            4,
            '\Magento\SomeModule\Model\Element\Proxy',
            'Invalid proxy class for ' . substr('\Magento\SomeModule\Model\Element\Proxy', 0, -5)
        );
        $this->_logMock->expects(
            $this->at(2)
        )->method(
            'add'
        )->with(
            4,
            '\Magento\SomeModule\Model\Nested\Element\Proxy',
            'Invalid proxy class for ' . substr('\Magento\SomeModule\Model\Nested\Element\Proxy', 0, -5)
        );
        $actual = $this->_model->collectEntities($this->_testFiles);
        $expected = array('Magento\App\Request\Http\Proxy');
        $this->assertEquals($expected, $actual);
    }
}
