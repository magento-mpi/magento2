<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_FullPageCache_Model_Placeholder_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_readerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configScopeMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    /**
     * @var Magento_FullPageCache_Model_Placeholder_Config
     */
    protected $_model;

    protected function setUp()
    {
        $this->_readerMock = $this->getMock(
            'Magento_FullPageCache_Model_Placeholder_Config_Reader',
            array(), array(), '', false
        );
        $this->_configScopeMock = $this->getMock('Magento_Config_ScopeInterface');
        $this->_cacheMock = $this->getMock('Magento_Config_CacheInterface');
        $cacheId = null;
        $this->_cacheMock->expects($this->any())->method('get')->will($this->returnValue(false));
        $this->_configScopeMock->expects($this->any())->method('getCurrentScope')->will($this->returnValue('global'));

        $this->_model = new Magento_FullPageCache_Model_Placeholder_Config(
            $this->_readerMock,
            $this->_configScopeMock,
            $this->_cacheMock,
            $cacheId
        );
    }

    public function testGetPlaceholdersExisted()
    {
        $testData = array(array('some' => 'data'));
        $data = array('someBlockInstanceName' => $testData);
        $this->_readerMock->expects($this->once())->method('read')->with('global')->will($this->returnValue($data));
        $this->_cacheMock->expects($this->once())->method('load')->will($this->returnValue(false));
        $this->assertEquals($testData, $this->_model->getPlaceholders('someBlockInstanceName'));
    }

    public function testGetPlaceholdersNotExisted()
    {
        $testData = array(array('some' => 'data'));
        $data = array('someBlockInstanceName' => $testData);
        $this->_cacheMock->expects($this->once())->method('load')->will($this->returnValue(false));
        $this->_readerMock->expects($this->once())->method('read')->with('global')->will($this->returnValue($data));
        $this->assertEquals(array(), $this->_model->getPlaceholders('notExistedKey'));
    }
}
