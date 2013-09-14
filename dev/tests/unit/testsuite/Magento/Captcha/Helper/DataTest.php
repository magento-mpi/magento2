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
        $this->_dirMock = $this->getMock('Magento\Core\Model\Dir', array(), array(), '', false, false);
    }

    protected function _getHelper($store, $config, $factory)
    {
        $storeManager = $this->getMockBuilder('Magento\Core\Model\StoreManager')
            ->disableOriginalConstructor()
            ->getMock();
        $storeManager->expects($this->any())
            ->method('getWebsite')
            ->will($this->returnValue($this->_getWebsiteStub()));
        $storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($store));

        $adapterMock = $this->getMockBuilder('Magento\Filesystem\Adapter\Local')
            ->getMock();
        $adapterMock->expects($this->any())
            ->method('isDirectory')
            ->will($this->returnValue(true));

        $filesystem = $this->getMock('Magento\Filesystem', array(), array(), '', false);

        $context = $this->getMock('Magento\Core\Helper\Context', array(), array(), '', false);

        return new \Magento\Captcha\Helper\Data(
            $context, $this->_dirMock, $storeManager, $config, $filesystem, $factory
        );
    }

    /**
     * @covers \Magento\Captcha\Helper\Data::getCaptcha
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

        $objectManager = $this->getMock('Magento\ObjectManager');
        $config = $this->_getConfigStub();

        $factoryMock = $this->getMock('Magento\Captcha\Model\CaptchaFactory', array(), array(), '', false);

        $factoryMock->expects($this->once())
            ->method('create')
            ->with('Magento\Captcha\Model\Zend')
            ->will($this->returnValue(
                new \Magento\Captcha\Model\DefaultModel($objectManager, array('formId' => 'user_create')))
            );

        $helper = $this->_getHelper($store, $config, $factoryMock);
        $this->assertInstanceOf('Magento\Captcha\Model\DefaultModel', $helper->getCaptcha('user_create'));
    }

    /**
     * @covers \Magento\Captcha\Helper\Data::getConfigNode
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

        $factoryMock = $this->getMock('Magento\Captcha\Model\CaptchaFactory', array(), array(), '', false);
        $object = $this->_getHelper($store, $this->_getConfigStub(), $factoryMock);
        $object->getConfigNode('enable');
    }

    public function testGetFonts()
    {
        $this->_dirMock->expects($this->once())
            ->method('getDir')
            ->with(\Magento\Core\Model\Dir::LIB)
            ->will($this->returnValue(TESTS_TEMP_DIR . '/lib'));

        $factoryMock = $this->getMock('Magento\Captcha\Model\CaptchaFactory', array(), array(), '', false);
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
     * @covers \Magento\Captcha\Model\DefaultModel::getImgDir
     * @covers \Magento\Captcha\Helper\Data::getImgDir
     */
    public function testGetImgDir()
    {
        $factoryMock = $this->getMock('Magento\Captcha\Model\CaptchaFactory', array(), array(), '', false);
        $this->_dirMock->expects($this->once())
            ->method('getDir')
            ->with(\Magento\Core\Model\Dir::MEDIA)
            ->will($this->returnValue(TESTS_TEMP_DIR . '/media'));

        $object = $this->_getHelper($this->_getStoreStub(), $this->_getConfigStub(), $factoryMock);
        $this->assertFileNotExists(TESTS_TEMP_DIR . '/captcha');
        $result = $object->getImgDir();
        $result = str_replace('/', DIRECTORY_SEPARATOR, $result);
        $this->assertStringStartsWith(TESTS_TEMP_DIR, $result);
        $this->assertStringEndsWith('captcha' . DIRECTORY_SEPARATOR . 'base' . DIRECTORY_SEPARATOR, $result);
    }

    /**
     * @covers \Magento\Captcha\Model\DefaultModel::getImgUrl
     * @covers \Magento\Captcha\Helper\Data::getImgUrl
     */
    public function testGetImgUrl()
    {
        $factoryMock = $this->getMock('Magento\Captcha\Model\CaptchaFactory', array(), array(), '', false);
        $object = $this->_getHelper($this->_getStoreStub(), $this->_getConfigStub(), $factoryMock);
        $this->assertEquals($object->getImgUrl(), 'http://localhost/pub/media/captcha/base/');
    }

    /**
     * Create Config Stub
     *
     * @return \Magento\Core\Model\Config
     */
    protected function _getConfigStub()
    {
        $config = $this->getMock(
            'Magento\Core\Model\Config',
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
     * @return \Magento\Core\Model\Website
     */
    protected function _getWebsiteStub()
    {
        $website = $this->getMock(
            'Magento\Core\Model\Website',
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
     * @return \Magento\Core\Model\Store
     */
    protected function _getStoreStub()
    {
        $store = $this->getMock(
            'Magento\Core\Model\Store',
            array('isAdmin', 'getConfig', 'getBaseUrl'),
            array(), '', false
        );

        $store->expects($this->any())
            ->method('getBaseUrl')
            ->will($this->returnValue('http://localhost/pub/media/'));

        return $store;
    }
}
