<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Store\Model\Config\Reader;

class StoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Store\Model\Config\Reader\Store
     */
    protected $_model;

    /**
     * @var \Magento\App\Config\ScopePool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_scopePullMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_initialConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_collectionFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appStateMock;

    protected function setUp()
    {
        $this->_scopePullMock = $this->getMock('Magento\App\Config\ScopePool', array(), array(), '', false);
        $this->_storeManagerMock = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $this->_initialConfigMock = $this->getMock('Magento\App\Config\Initial', array(), array(), '', false);
        $this->_collectionFactory = $this->getMock(
            'Magento\Store\Model\Resource\Config\Collection\ScopedFactory',
            array('create'),
            array(),
            '',
            false
        );
        $storeFactoryMock = $this->getMock('Magento\Store\Model\StoreFactory', array('create'), array(), '', false);
        $this->_storeMock = $this->getMock('Magento\Store\Model\Store', array(), array(), '', false);
        $storeFactoryMock->expects($this->any())->method('create')->will($this->returnValue($this->_storeMock));

        $this->_appStateMock = $this->getMock('Magento\App\State', array(), array(), '', false);
        $this->_appStateMock->expects($this->any())->method('isInstalled')->will($this->returnValue(true));

        $placeholderProcessor = $this->getMock(
            'Magento\Store\Model\Config\Processor\Placeholder',
            array(),
            array(),
            '',
            false
        );
        $placeholderProcessor->expects($this->any())->method('process')->will($this->returnArgument(0));
        $this->_model = new \Magento\Store\Model\Config\Reader\Store(
            $this->_initialConfigMock,
            $this->_scopePullMock,
            new \Magento\Store\Model\Config\Converter($placeholderProcessor),
            $this->_collectionFactory,
            $storeFactoryMock,
            $this->_appStateMock,
            $this->_storeManagerMock
        );
    }

    /**
     * @dataProvider readDataProvider
     * @param string|null $storeCode
     * @param PHPUnit_Framework_MockObject_Matcher_AnyInvokedCount $getStoreCount
     */
    public function testRead($storeCode, $getStoreExpectsCount)
    {
        $websiteCode = 'default';
        $storeId = 1;
        $websiteMock = $this->getMock('Magento\Store\Model\Website', array(), array(), '', false);
        $websiteMock->expects($this->any())->method('getCode')->will($this->returnValue($websiteCode));
        $this->_storeMock->expects($this->any())->method('getWebsite')->will($this->returnValue($websiteMock));
        $this->_storeMock->expects($this->any())->method('load')->with($storeCode);
        $this->_storeMock->expects($this->any())->method('getId')->will($this->returnValue($storeId));

        $dataMock = $this->getMock('Magento\App\Config\Data', array(), array(), '', false);
        $dataMock->expects(
            $this->any()
        )->method(
            'getValue'
        )->will(
            $this->returnValue(array('config' => array('key0' => 'website_value0', 'key1' => 'website_value1')))
        );

        $dataMock->expects(
            $this->once()
        )->method(
            'getSource'
        )->will(
            $this->returnValue(array('config' => array('key0' => 'website_value0', 'key1' => 'website_value1')))
        );
        $this->_scopePullMock->expects(
            $this->once()
        )->method(
            'getScope'
        )->with(
            'website',
            $websiteCode
        )->will(
            $this->returnValue($dataMock)
        );

        $this->_initialConfigMock->expects(
            $this->once()
        )->method(
            'getData'
        )->with(
            "stores|{$storeCode}"
        )->will(
            $this->returnValue(array('config' => array('key1' => 'store_value1', 'key2' => 'store_value2')))
        );
        $this->_collectionFactory->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            array('scope' => 'stores', 'scopeId' => $storeId)
        )->will(
            $this->returnValue(
                array(
                    new \Magento\Object(array('path' => 'config/key1', 'value' => 'store_db_value1')),
                    new \Magento\Object(array('path' => 'config/key3', 'value' => 'store_db_value3'))
                )
            )
        );
        $this->_storeManagerMock->expects(
            $getStoreExpectsCount
        )->method(
            'getStore'
        )->will(
            $this->returnValue($this->_storeMock)
        );
        $expectedData = array(
            'config' => array(
                'key0' => 'website_value0',
                'key1' => 'store_db_value1',
                'key2' => 'store_value2',
                'key3' => 'store_db_value3'
            )
        );
        $this->assertEquals($expectedData, $this->_model->read($storeCode));
    }

    public function readDataProvider()
    {
        return array(array('default', $this->never()), array(null, $this->once()));
    }
}
