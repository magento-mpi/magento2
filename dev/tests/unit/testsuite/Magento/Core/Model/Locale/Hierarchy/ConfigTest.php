<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Locale_Hierarchy_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Locale_Hierarchy_Config
     */
    protected $_model;

    /**
     * @var Magento_Core_Model_Locale_Hierarchy_Config_Reader
     */
    protected $_configReaderMock;

    /**
     * @var \Magento\Config\CacheInterface
     */
    protected $_cacheMock;

    /**
     * @var string
     */
    protected $_cacheId;

    protected function setUp()
    {
        $this->_configReaderMock = $this->getMock(
            'Magento_Core_Model_Locale_Hierarchy_Config_Reader', array(), array(), '', false
        );
        $this->_cacheMock = $this->getMock('Magento\Config\CacheInterface');
        $this->_cacheId = 'customCacheId';

        $this->_model = new Magento_Core_Model_Locale_Hierarchy_Config(
            $this->_configReaderMock,
            $this->_cacheMock,
            $this->_cacheId
        );
    }

    /**
     * @covers Magento_Core_Model_Locale_Hierarchy_Config::getHierarchy
     */
    public function testGetHierarchyCached()
    {
        $expectedData = array('key' => 'value');

        $this->_cacheMock->expects($this->once())
            ->method('get')
            ->with('global', $this->_cacheId)
            ->will($this->returnValue($expectedData));

        $this->assertEquals($expectedData, $this->_model->getHierarchy());
    }

    /**
     * @covers Magento_Core_Model_Locale_Hierarchy_Config::getHierarchy
     */
    public function testGetHierarchyNonCached()
    {
        $expectedData = array('key' => 'value');

        $this->_cacheMock->expects($this->once())
            ->method('get')
            ->with('global', $this->_cacheId)
            ->will($this->returnValue(false));

        $this->_configReaderMock->expects($this->once())
            ->method('read')
            ->with('global')
            ->will($this->returnValue($expectedData));

        $this->_cacheMock->expects($this->once())
            ->method('put')
            ->with($expectedData, 'global', $this->_cacheId);

        $this->assertEquals($expectedData, $this->_model->getHierarchy());
    }
}