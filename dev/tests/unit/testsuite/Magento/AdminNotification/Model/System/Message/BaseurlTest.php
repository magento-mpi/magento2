<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_AdminNotification_Model_System_Message_BaseurlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_AdminNotification_Model_System_Message_Baseurl
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlBuilderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dataCollectionMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configDataMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_iteratorMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    protected function setUp()
    {
        $helper = new Magento_Test_Helper_ObjectManager($this);
        $this->_configMock = $this->getMock('Magento_Core_Model_Config', array(), array(), '', false);
        $this->_urlBuilderMock = $this->getMock('Magento_Core_Model_UrlInterface');

        $this->_storeManagerMock = $this->getMock('Magento_Core_Model_StoreManagerInterface');
        $configFactoryMock = $this->getMock('Magento_Core_Model_Config_ValueFactory', array('create'),
            array(), '', false
        );
        $this->_configDataMock = $this->getMock('Magento_Core_Model_Config_Value',
            array('getScope', 'getScopeId', 'getCollection'),
            array(), '', false
        );
        $this->_dataCollectionMock = $this->getMock('Magento_Core_Model_Resource_Config_Data_Collection',
            array(), array(), '', false
        );

        $this->_iteratorMock = $this->getMock('Iterator');
        $this->_dataCollectionMock->expects($this->any())
            ->method('getIterator')->will($this->returnValue($this->_iteratorMock));

        $configFactoryMock->expects($this->any())
            ->method('create')->will($this->returnValue($this->_configDataMock));
        $this->_configDataMock->expects($this->any())
            ->method('getCollection')->will($this->returnValue($this->_dataCollectionMock));

        $arguments = array(
            'config' => $this->_configMock,
            'urlBuilder' => $this->_urlBuilderMock,
            'configValueFactory' => $configFactoryMock,
            'storeManager' => $this->_storeManagerMock,
        );
        $this->_model = $helper->getObject('Magento_AdminNotification_Model_System_Message_Baseurl', $arguments);
    }

    public function testGetSeverity()
    {
        $this->assertEquals(
            Magento_AdminNotification_Model_System_MessageInterface::SEVERITY_CRITICAL,
            $this->_model->getSeverity(),
            'Invalid message severity type'
        );
    }

    public function testGetConfigUrlWithDefaultUnsecureAndSecureBaseUrl()
    {
        $map = array(
            array(Magento_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL, 'default', null,
                Magento_Core_Model_Store::BASE_URL_PLACEHOLDER
            ),
            array(Magento_Core_Model_Store::XML_PATH_SECURE_BASE_URL, 'default', null,
                Magento_Core_Model_Store::BASE_URL_PLACEHOLDER
            ),
        );
        $this->_configMock->expects($this->exactly(2))->method('getValue')->will($this->returnValueMap($map));
        $this->_urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('adminhtml/system_config/edit', array('section' => 'web'))
            ->will($this->returnValue('http://some_url'));

        $this->assertContains('http://some_url', (string)$this->_model->getText());
    }

    public function testGetConfigUrlWithoutSavedData()
    {
        $this->_configMock->expects($this->any())->method('getNode')->will($this->returnValue(null));
        $this->_urlBuilderMock->expects($this->never())->method('getUrl');
    }

    /**
     * @dataProvider getConfigUrlWithSavedDataForStoreScopeDataProvider
     */
    public function testGetConfigUrlWithSavedDataForScopes($scope, $urlParam, $storeMethod)
    {
        $this->_configMock->expects($this->any())->method('getNode')->will($this->returnValue(null));
        $this->_iteratorMock->expects($this->once())->method('valid')->will($this->returnValue(true));
        $this->_iteratorMock->expects($this->once())->method('current')
            ->will($this->returnValue($this->_configDataMock));

        $this->_configDataMock->expects($this->once())->method('getScopeId')->will($this->returnValue(1));

        $storeMock = $this->getMock('Magento_Core_Model_Store', array(), array(), '', false);
        $this->_storeManagerMock->expects($this->once())->method($storeMethod)
            ->with(1)->will($this->returnValue($storeMock));
        $storeMock->expects($this->once())->method('getCode')->will($this->returnValue('some_code'));

        $this->_configDataMock->expects($this->any())->method('getScope')->will($this->returnValue($scope));
        $this->_urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('adminhtml/system_config/edit', array('section' => 'web', $urlParam => 'some_code'))
            ->will($this->returnValue('http://some_url'));

        $this->assertContains('http://some_url', (string)$this->_model->getText());
    }

    public function getConfigUrlWithSavedDataForStoreScopeDataProvider()
    {
        return array(
            'storeScope' => array('stores', 'store', 'getStore'),
            'websiteScope' => array('websites', 'website', 'getWebsite'),
        );
    }

    public function testIsDisplayedWithEmptyConfigUrl()
    {
        $this->_configMock->expects($this->any())->method('getValue')
            ->will($this->returnValue(Magento_Core_Model_Store::BASE_URL_PLACEHOLDER));
        $this->_urlBuilderMock->expects($this->once())->method('getUrl')->will($this->returnValue(''));
        $this->assertFalse($this->_model->isDisplayed());
    }

    public function testIsDisplayedWithNotEmptyConfigUrl()
    {
        $this->_configMock->expects($this->any())->method('getValue')
            ->will($this->returnValue(Magento_Core_Model_Store::BASE_URL_PLACEHOLDER));
        $this->_urlBuilderMock->expects($this->once())->method('getUrl')->will($this->returnValue('http://some_url'));
        $this->assertTrue($this->_model->isDisplayed());
    }

    public function testGetIdentity()
    {
        $this->_configMock->expects($this->any())->method('getValue')
            ->will($this->returnValue(Magento_Core_Model_Store::BASE_URL_PLACEHOLDER));
        $this->_urlBuilderMock->expects($this->once())->method('getUrl')->will($this->returnValue('some_url'));
        $this->assertEquals(md5('BASE_URLsome_url'), $this->_model->getIdentity());
    }

    public function testGetText()
    {
        $expected = '{{base_url}} is not recommended to use in a production environment';
        $this->assertContains($expected, (string)$this->_model->getText());
    }
}
