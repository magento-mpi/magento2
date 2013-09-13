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
     * @var Magento_Config_CacheInterface
     */
    protected $_cacheMock;

    /**
     * @var string
     */
    protected $_cacheId;

    /**
     * @var array
     */
    protected $_testData;

    protected function setUp()
    {
        $this->_configReaderMock = $this->getMock(
            'Magento_Core_Model_Locale_Hierarchy_Config_Reader', array(), array(), '', false
        );
        $this->_cacheMock = $this->getMock('Magento_Config_CacheInterface');
        $this->_cacheId = 'customCacheId';

        $this->_testData = array('key' => 'value');

        $this->_cacheMock->expects($this->once())
            ->method('load')
            ->with($this->_cacheId)
            ->will($this->returnValue($this->_testData));

        $this->_model = new Magento_Core_Model_Locale_Hierarchy_Config(
            $this->_configReaderMock,
            $this->_cacheMock,
            $this->_cacheId
        );
    }

    public function testGetHierarchy()
    {
        $this->assertEquals($this->_testData, $this->_model->getHierarchy());
    }
}
