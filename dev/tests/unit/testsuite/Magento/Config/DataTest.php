<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Config_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Config_Data
     */
    protected $_model;

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

    protected function setUp()
    {
        $this->_readerMock = $this->getMock('Magento_Config_ReaderInterface');
        $this->_configScopeMock = $this->getMock('Magento_Config_ScopeInterface');
        $this->_cacheMock = $this->getMock('Magento_Config_CacheInterface');

        $this->_model = new Magento_Config_Data(
            $this->_readerMock,
            $this->_configScopeMock,
            $this->_cacheMock,
            'tag'
        );
    }

    /**
     * @param string $path
     * @param mixed $expectedValue
     * @param string $default
     * @dataProvider getValueByPathDataProvider
     */
    public function testGetValueByPath($path, $expectedValue, $default)
    {
        $testData = array(
            'key_1' => array(
                'key_1.1' => array(
                    'key_1.1.1' => 'value_1.1.1',
                ),
                'key_1.2' => array(
                    'some' => 'arrayValue',
                ),
            )
        );
        $this->_cacheMock->expects($this->any())->method('get')->will($this->returnValue(array()));
        $this->_model->merge($testData);
        $this->assertEquals($expectedValue, $this->_model->get($path, $default));
    }

    public function getValueByPathDataProvider()
    {
        return array(
            array('key_1/key_1.1/key_1.1.1', 'value_1.1.1', 'error'),
            array('key_1/key_1.2', array('some' => 'arrayValue'), 'error'),
            array(
                'key_1',
                array('key_1.1' => array('key_1.1.1' => 'value_1.1.1'),'key_1.2' => array('some' => 'arrayValue')),
                'error'
            ),
            array('key_1/notExistedKey', 'defaultValue', 'defaultValue'),
        );
    }

    public function testGetScopeSwitchingWithNonCachedData()
    {
        $testValue = array('some' => 'testValue');

        /** change current area */
        $this->_configScopeMock->expects($this->any())
            ->method('getCurrentScope')
            ->will($this->returnValue('adminhtml'));

        /** set empty cache data */
        $this->_cacheMock->expects($this->once())
            ->method('get')
            ->with('adminhtml', 'tag')
            ->will($this->returnValue(false));

        /** get data from reader  */
        $this->_readerMock->expects($this->once())
            ->method('read')
            ->with('adminhtml')
            ->will($this->returnValue($testValue));

        /** test cache saving  */
        $this->_cacheMock->expects($this->once())->method('put')->with($testValue, 'adminhtml', 'tag');

        /** test config value existence */
        $this->assertEquals('testValue', $this->_model->get('some'));

        /** test preventing of double config data loading from reader */
        $this->assertEquals('testValue', $this->_model->get('some'));
    }

    public function testGetScopeSwitchingWithCachedData()
    {
        $testValue = array('some' => 'testValue');

        /** change current area */
        $this->_configScopeMock->expects($this->any())
            ->method('getCurrentScope')
            ->will($this->returnValue('adminhtml'));

        /** set cache data */
        $this->_cacheMock->expects($this->once())
            ->method('get')
            ->with('adminhtml', 'tag')
            ->will($this->returnValue($testValue));

        /** test preventing of getting data from reader  */
        $this->_readerMock->expects($this->never())->method('read');

        /** test preventing of cache saving  */
        $this->_cacheMock->expects($this->never())->method('put');

        /** test config value existence */
        $this->assertEquals('testValue', $this->_model->get('some'));

        /** test preventing of double config data loading from reader */
        $this->assertEquals('testValue', $this->_model->get('some'));
    }
}
