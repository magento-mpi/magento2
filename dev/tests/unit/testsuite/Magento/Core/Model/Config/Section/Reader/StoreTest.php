<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Section_Reader_StoreTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Config_Section_Reader_Store
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sectionPullMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_initialConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_collectionFactory;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appStateMock;

    protected function setUp()
    {
        $this->_sectionPullMock = $this->getMock('Magento_Core_Model_Config_SectionPool', array(), array(), '', false);
        $this->_initialConfigMock = $this->getMock('Magento_Core_Model_Config_Initial', array(), array(), '', false);
        $this->_collectionFactory = $this->getMock(
            'Magento_Core_Model_Resource_Config_Value_Collection_ScopedFactory',
            array('create'),
            array(),
            '',
            false
        );
        $storeFactoryMock = $this->getMock('Magento_Core_Model_StoreFactory', array('create'), array(), '', false);
        $this->_storeMock = $this->getMock('Magento_Core_Model_Store', array(), array(), '', false);
        $storeFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_storeMock));

        $this->_appStateMock = $this->getMock('Magento_Core_Model_App_State', array(), array(), '', false);
        $this->_appStateMock->expects($this->any())
            ->method('isInstalled')
            ->will($this->returnValue(true));

        $placeholderProcessor = $this->getMock(
            'Magento_Core_Model_Config_Section_Processor_Placeholder',
            array(),
            array(),
            '',
            false
        );
        $placeholderProcessor->expects($this->any())
            ->method('process')
            ->will($this->returnArgument(0));
        $this->_model = new Magento_Core_Model_Config_Section_Reader_Store(
            $this->_initialConfigMock,
            $this->_sectionPullMock,
            new Magento_Core_Model_Config_Section_Store_Converter($placeholderProcessor),
            $this->_collectionFactory,
            $storeFactoryMock,
            $this->_appStateMock
        );
    }

    public function testRead()
    {
        $websiteCode = 'default';
        $storeCode = 'default';
        $storeId = 1;
        $websiteMock = $this->getMock('Magento_Core_Model_Website', array(), array(), '', false);
        $websiteMock->expects($this->any())->method('getCode')->will($this->returnValue($websiteCode));
        $this->_storeMock->expects($this->any())->method('getWebsite')->will($this->returnValue($websiteMock));
        $this->_storeMock->expects($this->once())
            ->method('load')
            ->with($storeCode);
        $this->_storeMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($storeId));

        $sectionMock = $this->getMock('Magento_Core_Model_Config_Data', array(), array(), '', false);
        $sectionMock->expects($this->once())->method('getValue')->will($this->returnValue(array(
            'config' => array('key0' => 'website_value0', 'key1' => 'website_value1'),
        )));
        $this->_sectionPullMock->expects($this->once())
            ->method('getSection')
            ->with('website', $websiteCode)
            ->will($this->returnValue($sectionMock));

        $this->_initialConfigMock->expects($this->any())
            ->method('getStore')
            ->with($storeCode)
            ->will($this->returnValue(array(
                'config' => array('key1' => 'store_value1', 'key2' => 'store_value2'),
            )));
        $this->_collectionFactory->expects($this->once())
            ->method('create')
            ->with(array('scope' => 'stores', 'scopeId' => $storeId))
            ->will($this->returnValue(array(
                new \Magento\Object(array('path' => 'config/key1', 'value' => 'store_db_value1')),
                new \Magento\Object(array('path' => 'config/key3', 'value' => 'store_db_value3')),
            )));
        $expectedData = array(
            'config' => array(
                'key0' => 'website_value0', // value from website scope
                'key1' => 'store_db_value1',
                'key2' => 'store_value2', // value that has not been overridden in DB
                'key3' => 'store_db_value3'
            ),
        );
        $this->assertEquals($expectedData, $this->_model->read($storeCode));
    }
}
