<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Captcha_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirMock;

    protected function setUp()
    {
        $this->_dirMock = $this->getMock('Magento_Core_Model_Dir', array(), array(), '', false, false);
    }

    protected function _getHelper($store, $config, $factory)
    {
        $storeManager = $this->getMockBuilder('Magento_Core_Model_StoreManager')
            ->disableOriginalConstructor()
            ->getMock();
        $storeManager->expects($this->any())
            ->method('getWebsite')
            ->will($this->returnValue($this->_getWebsiteStub()));
        $storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($store));

        $adapterMock = $this->getMockBuilder('Magento_Filesystem_Adapter_Local')
            ->getMock();
        $adapterMock->expects($this->any())
            ->method('isDirectory')
            ->will($this->returnValue(true));

        $filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);

        $context = $this->getMock('Magento_Core_Helper_Context', array(), array(), '', false);

        return new Magento_Captcha_Helper_Data($context, $this->_dirMock, $storeManager, $config, $filesystem, $factory);
    }

    /**
     * @covers Magento_Captcha_Helper_Data::getCaptcha
     */
    public function testGetCaptcha()
    {
        $store = $this->_getStoreStub();
        $store->expects($this->once())
            ->method('isAdmin')
            ->will($this->returnValue(false));

        $store->expects($this->once())
            ->method('getConfig')
            ->with('customer/captcha/type')
            ->will($this->returnValue('zend'));

        $objectManager = $this->getMock('Magento_ObjectManager');
        $config = $this->_getConfigStub();

        $factoryMock = $this->getMock('Magento_Captcha_Model_CaptchaFactory', array(), array(), '', false);

        $factoryMock->expects($this->once())
            ->method('create')
            ->with('Magento_Captcha_Model_Zend')
        $config->expects($this->once())
            ->method('getModelInstance')
            ->with('Magento_Captcha_Model_Zend')
            ->will($this->returnValue(
            new Magento_Captcha_Model_Default($objectManager, array('formId' => 'user_create'))));

        $helper = $this->_getHelper($store, $config, $factoryMock);
        $this->assertInstanceOf('Magento_Captcha_Model_Default', $helper->getCaptcha('user_create'));
        $helper = $this->_getHelper($store, $config);
        $this->assertInstanceOf('Magento_Captcha_Model_Default', $helper->getCaptcha('user_create'));
    }

    /**
     * @covers Magento_Captcha_Helper_Data::getConfigNode
     */
    public function testGetConfigNode()
    {
        $store = $this->_getStoreStub();
        $store->expects($this->once())
            ->method('isAdmin')
            ->will($this->returnValue(false));

        $store->expects($this->once())
            ->method('getConfig')
            ->with('customer/captcha/enable')
            ->will($this->returnValue('1'));

        $factoryMock = $this->getMock('Magento_Captcha_Model_CaptchaFactory', array(), array(), '', false);
        $object = $this->_getHelper($store, $this->_getConfigStub(), $factoryMock);
        $object->getConfigNode('enable');
    }

    public function testGetFonts()
    {
        $this->_dirMock->expects($this->once())
            ->method('getDir')
            ->with(Magento_Core_Model_Dir::LIB)
            ->will($this->returnValue(TESTS_TEMP_DIR . '/lib'));

        $factoryMock = $this->getMock('Magento_Captcha_Model_CaptchaFactory', array(), array(), '', false);
        $object = $this->_getHelper($this->_getStoreStub(), $this->_getConfigStub(), $factoryMock);
        $fonts = $object->getFonts();
        $this->assertArrayHasKey('font_code', $fonts); // fixture
        $this->assertArrayHasKey('label', $fonts['font_code']);
        $this->assertArrayHasKey('path', $fonts['font_code']);
        $this->assertEquals('Label', $fonts['font_code']['label']);
        $this->assertStringStartsWith(TESTS_TEMP_DIR, $fonts['font_code']['path']);
        $this->assertStringEndsWith('path/to/fixture.ttf', $fonts['font_code']['path']);
    }

    /**
     * @covers Magento_Captcha_Model_Default::getImgDir
     * @covers Magento_Captcha_Helper_Data::getImgDir
     */
    public function testGetImgDir()
    {
        $factoryMock = $this->getMock('Magento_Captcha_Model_CaptchaFactory', array(), array(), '', false);
        $this->_dirMock->expects($this->once())
            ->method('getDir')
            ->with(Magento_Core_Model_Dir::MEDIA)
            ->will($this->returnValue(TESTS_TEMP_DIR . '/media'));

        $object = $this->_getHelper($this->_getStoreStub(), $this->_getConfigStub(), $factoryMock);
        $this->assertFileNotExists(TESTS_TEMP_DIR . '/captcha');
        $result = $object->getImgDir();
        $result = str_replace('/', DIRECTORY_SEPARATOR, $result);
        $this->assertStringStartsWith(TESTS_TEMP_DIR, $result);
        $this->assertStringEndsWith('captcha' . DIRECTORY_SEPARATOR . 'base' . DIRECTORY_SEPARATOR, $result);
    }

    /**
     * @covers Magento_Captcha_Model_Default::getImgUrl
     * @covers Magento_Captcha_Helper_Data::getImgUrl
     */
    public function testGetImgUrl()
    {
        $factoryMock = $this->getMock('Magento_Captcha_Model_CaptchaFactory', array(), array(), '', false);
        $object = $this->_getHelper($this->_getStoreStub(), $this->_getConfigStub(), $factoryMock);
        $this->assertEquals($object->getImgUrl(), 'http://localhost/pub/media/captcha/base/');
    }

    /**
     * Create Config Stub
     *
     * @return Magento_Core_Model_Config
     */
    protected function _getConfigStub()
    {
        $config = $this->getMock(
            'Magento_Core_Model_Config',
            array('getValue'),
            array(), '', false
        );

        $configData = array(
            'font_code' => array(
                'label' => 'Label',
                'path'  => 'path/to/fixture.ttf',
            )
        );

        $config->expects($this->any())
            ->method('getValue')
            ->with('captcha/fonts', 'default')
            ->will($this->returnValue($configData));
        return $config;
    }

    /**
     * Create Website Stub
     *
     * @return Magento_Core_Model_Website
     */
    protected function _getWebsiteStub()
    {
        $website = $this->getMock(
            'Magento_Core_Model_Website',
            array('getCode'),
            array(), '', false
        );

        $website->expects($this->any())
            ->method('getCode')
            ->will($this->returnValue('base'));

        return $website;
    }

    /**
     * Create store stub
     *
     * @return Magento_Core_Model_Store
     */
    protected function _getStoreStub()
    {
        $store = $this->getMock(
            'Magento_Core_Model_Store',
            array('isAdmin', 'getConfig', 'getBaseUrl'),
            array(), '', false
        );

        $store->expects($this->any())
            ->method('getBaseUrl')
            ->will($this->returnValue('http://localhost/pub/media/'));

        return $store;
    }
}
