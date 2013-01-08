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
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_object = new Mage_Captcha_Helper_Data(new Mage_Core_Model_Dir(TESTS_TEMP_DIR));
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
            ->with('customer/captcha/type', null)
            ->will($this->returnValue('zend'));
        $this->_object->setStore($store);

        $config = $this->_getConfigStub();
        $config->expects($this->once())
            ->method('getModelInstance')
            ->with('Mage_Captcha_Model_Zend',
                array(
                    'params' => array('formId' => 'user_create', 'helper' => $this->_object)
                )
            )
            ->will($this->returnValue(new Mage_Captcha_Model_Zend(array('formId' => 'user_create'))));
        $this->_object->setConfig($config);

        $this->assertInstanceOf('Mage_Captcha_Model_Zend', $this->_object->getCaptcha('user_create'));
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
            ->with('customer/captcha/enable', null)
            ->will($this->returnValue('1'));
        $this->_object->setStore($store);
        $this->_object->getConfigNode('enable');
    }

    public function testGetFonts()
    {
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
        $this->assertFileNotExists(TESTS_TEMP_DIR . '/captcha');
        $result = $this->_object->getImgDir();
        $this->assertFileExists($result);
        $this->assertStringStartsWith(TESTS_TEMP_DIR, $result);
        $this->assertStringEndsWith('captcha' . DIRECTORY_SEPARATOR . 'base', $result);
    }

    /**
     * @covers Mage_Captcha_Model_Zend::getImgUrl
     * @covers Mage_Captcha_Helper_Data::getImgUrl
     */
    public function testGetImgUrl()
    {
        $this->assertEquals($this->_object->getImgUrl(), 'http://localhost/pub/media/captcha/base/');
    }

    /**
     * Create Config Stub
     * @return Mage_Core_Model_Config
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
     * @return Mage_Core_Model_Website
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
     * @return Mage_Core_Model_Store
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
