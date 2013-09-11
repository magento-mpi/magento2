<?php
/**
 * Test class for \Magento\Core\Model\Config\Fieldset
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Config_FieldsetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\Config\Modules\Reader
     */
    protected $_configReaderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\Cache\Type\Config
     */
    protected $_cacheTypeMock;

    protected function setUp()
    {
        $this->_configReaderMock = $this->getMock(
            '\Magento\Core\Model\Config\Modules\Reader', array(), array(), '', false
        );
        $this->_cacheTypeMock = $this->getMock('Magento\Core\Model\Cache\Type\Config', array(), array(), '', false);
    }

    protected function tearDown()
    {
        $this->_configReaderMock = null;
        $this->_cacheTypeMock = null;
    }

    public function testConstructorCacheExists()
    {
        $cachedConfig = '<config/>';
        $this->_cacheTypeMock->expects($this->once())
            ->method('load')
            ->with('fieldset_config')
            ->will($this->returnValue($cachedConfig));
        $model = new \Magento\Core\Model\Config\Fieldset($this->_configReaderMock, $this->_cacheTypeMock);
        $this->assertInstanceOf('\Magento\Simplexml\Element', $model->getNode());
    }

    public function testConstructorNoCacheExists()
    {
        $config = new \Magento\Core\Model\Config\Base('<config/>');
        $this->_cacheTypeMock->expects($this->once())
            ->method('load')
            ->with('fieldset_config')
            ->will($this->returnValue(false));
        $this->_configReaderMock->expects($this->once())
            ->method('loadModulesConfiguration')
            ->with('fieldset.xml')
            ->will($this->returnValue($config));
        $this->_cacheTypeMock->expects($this->once())
            ->method('save')
            ->with("<?xml version=\"1.0\"?>\n<config/>\n");
        $model = new \Magento\Core\Model\Config\Fieldset($this->_configReaderMock, $this->_cacheTypeMock);
        $this->assertInstanceOf('\Magento\Simplexml\Element', $model->getNode());
    }
}
