<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Captcha
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Captcha_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * Fixture for testing getFonts()
     */
    const FONT_FIXTURE = '<fonts><font_code><label>Label</label><path>path/to/fixture.ttf</path></font_code></fonts>';

    /**
     * @var Mage_Captcha_Helper_Data
     */
    protected $_object;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirMock;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_dirMock = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false, false);
        $this->_object = new Mage_Captcha_Helper_Data(
            $this->_dirMock,
            $this->getMock('Mage_Core_Model_Translate', array(), array(), '', false, false)
        );
        $this->_object->setConfig($this->_getConfigStub());
        $this->_object->setWebsite($this->_getWebsiteStub());
        $this->_object->setStore($this->_getStoreStub());
    }

    /**
     * @covers Mage_Captcha_Helper_Data::getCaptcha
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

        $objectManager = $this->getMock('Magento_ObjectManager_Zend', array(), array(), '', false);
        $config = $this->_getConfigStub();
        $config->expects($this->once())
            ->method('getModelInstance')
            ->with('Mage_Captcha_Model_Zend')
            ->will($this->returnValue(new Mage_Captcha_Model_Zend($objectManager, array('formId' => 'user_create'))));

        $helper = $this->_getHelper($store, $config);
        $this->assertInstanceOf('Mage_Captcha_Model_Zend', $helper->getCaptcha('user_create'));
    }

    /**
     * @covers Mage_Captcha_Helper_Data::getConfigNode
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
        $object = $this->_getHelper($store, $this->_getConfigStub());
        $object->getConfigNode('enable');
    }

    public function testGetFonts()
    {
        $this->_dirMock->expects($this->once())
            ->method('getDir')
            ->with(Mage_Core_Model_Dir::LIB)
            ->will($this->returnValue(TESTS_TEMP_DIR . '/lib'));
        $fonts = $this->_object->getFonts();
        $this->assertArrayHasKey('font_code', $fonts); // fixture
        $this->assertArrayHasKey('label', $fonts['font_code']);
        $this->assertArrayHasKey('path', $fonts['font_code']);
        $this->assertEquals('Label', $fonts['font_code']['label']);
        $this->assertStringStartsWith(TESTS_TEMP_DIR, $fonts['font_code']['path']);
        $this->assertStringEndsWith('path/to/fixture.ttf', $fonts['font_code']['path']);
    }

    /**
     * @covers Mage_Captcha_Model_Zend::getImgDir
     * @covers Mage_Captcha_Helper_Data::getImgDir
     */
    public function testGetImgDir()
    {
        $object = $this->_getHelper($this->_getStoreStub(), $this->_getConfigStub());
        $this->assertFileNotExists(TESTS_TEMP_DIR . '/captcha');
        $result = $object->getImgDir();
        $result = str_replace('/', DIRECTORY_SEPARATOR, $result);
        $this->assertFileExists($result);
        $this->assertStringStartsWith(TESTS_TEMP_DIR, $result);
        $this->assertStringEndsWith('captcha' . DIRECTORY_SEPARATOR . 'base' . DIRECTORY_SEPARATOR, $result);
    }

    /**
     * @covers Mage_Captcha_Model_Zend::getImgUrl
     * @covers Mage_Captcha_Helper_Data::getImgUrl
     */
    public function testGetImgUrl()
    {
        $object = $this->_getHelper($this->_getStoreStub(), $this->_getConfigStub());
        $this->assertEquals($object->getImgUrl(), 'http://localhost/pub/media/captcha/base/');
    }

    /**
     * Create Config Stub
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getConfigStub()
    {
        $config = $this->getMock(
            'Mage_Core_Model_Config',
            array('getNode', 'getModelInstance'),
            array(), '', false
        );

        $config->expects($this->any())
            ->method('getNode')
            ->will($this->returnValue(new SimpleXMLElement(self::FONT_FIXTURE)));
        return $config;
    }

    /**
     * Create Website Stub
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getWebsiteStub()
    {
        $website = $this->getMock(
            'Mage_Core_Model_Website',
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
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getStoreStub()
    {
        $store = $this->getMock(
            'Mage_Core_Model_Store',
            array('isAdmin', 'getConfig', 'getBaseUrl'),
            array(), '', false
        );

        $store->expects($this->any())
            ->method('getBaseUrl')
            ->will($this->returnValue('http://localhost/pub/media/'));

        return $store;
    }
}
