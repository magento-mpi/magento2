<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Helper_DataTest extends Mage_Backend_Area_TestCase
{
    /**
     * @var Mage_Backend_Helper_Data
     */
    protected $_helper;

    /**
     * @var Mage_Backend_Model_Auth
     */
    protected $_auth;

    protected function setUp()
    {
        parent::setUp();

        Mage::getConfig()->setCurrentAreaCode(Mage_Core_Model_App_Area::AREA_ADMINHTML);
        $this->_helper = Mage::helper('Mage_Backend_Helper_Data');
    }

    protected function tearDown()
    {
        $this->_helper = null;
        $this->_auth = null;
        Mage::getConfig()->setCurrentAreaCode(null);
    }

    /**
     * Performs user login
     */
    protected  function _login()
    {
        Mage::getSingleton('Mage_Backend_Model_Url')->turnOffSecretKey();
        $this->_auth = Mage::getSingleton('Mage_Backend_Model_Auth');
        $this->_auth->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);
    }

    /**
     * Performs user logout
     */
    protected function _logout()
    {
        $this->_auth->logout();
        Mage::getSingleton('Mage_Backend_Model_Url')->turnOnSecretKey();
    }

    /**
     * @covers Mage_Backend_Helper_Data::getPageHelpUrl
     * @covers Mage_Backend_Helper_Data::setPageHelpUrl
     * @covers Mage_Backend_Helper_Data::addPageHelpUrl
     */
    public function testPageHelpUrl()
    {
        Mage::app()->getRequest()
            ->setControllerModule('dummy')
            ->setControllerName('index')
            ->setActionName('test');


        $expected = 'http://www.magentocommerce.com/gethelp/en_US/dummy/index/test/';
        $this->assertEquals($expected, $this->_helper->getPageHelpUrl(), 'Incorrect help Url');

        $this->_helper->addPageHelpUrl('dummy');
        $expected .= 'dummy';
        $this->assertEquals($expected, $this->_helper->getPageHelpUrl(), 'Incorrect help Url suffix');
    }

    /**
     * @covers Mage_Backend_Helper_Data::getCurrentUserId
     */
    public function testGetCurrentUserId()
    {
        $this->assertFalse($this->_helper->getCurrentUserId());

        /**
         * perform login
         */
        Mage::getSingleton('Mage_Backend_Model_Url')->turnOffSecretKey();

        $auth = Mage::getModel('Mage_Backend_Model_Auth');
        $auth->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);
        $this->assertEquals(1, $this->_helper->getCurrentUserId());

        /**
         * perform logout
         */
        $auth->logout();
        Mage::getSingleton('Mage_Backend_Model_Url')->turnOnSecretKey();

        $this->assertFalse($this->_helper->getCurrentUserId());
    }

    /**
     * @covers Mage_Backend_Helper_Data::prepareFilterString
     * @covers Mage_Backend_Helper_Data::decodeFilter
     */
    public function testPrepareFilterString()
    {
        $expected = array(
            'key1' => 'val1',
            'key2' => 'val2',
            'key3' => 'val3',
        );

        $filterString = base64_encode('key1='.rawurlencode('val1').'&key2=' . rawurlencode('val2') . '&key3=val3');
        $actual = $this->_helper->prepareFilterString($filterString);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @magentoConfigFixture admin/routers/adminhtml/args/frontName admin
     */
    public function testGetHomePageUrl()
    {
        $this->assertStringEndsWith(
            'index.php/backend/admin/', $this->_helper->getHomePageUrl(), 'Incorrect home page URL'
        );
    }

    /**
     * Test testSwitchBackendInterfaceLocale data provider
     *
     * @return array
     */
    public function switchBackendInterfaceLocaleDataProvider()
    {
        return array(
            'case1' => array(
                'locale' => 'de_DE',
                'stringToTranslate' => 'Customer View',
                'expectedTranslation' => 'Kundenansicht',
            ),
            'case2' => array(
                'locale' => 'en_US',
                'stringToTranslate' => 'Customer View',
                'expectedTranslation' => 'Customer View',
            ),
        );
    }

    /**
     * Test for switchBackendInterfaceLocale method
     *
     * @param string $locale
     * @param string $stringToTranslate
     * @param string $expectedTranslation
     * @dataProvider switchBackendInterfaceLocaleDataProvider
     * @covers Mage_Backend_Helper_Data::switchBackendInterfaceLocale
     */
    public function testSwitchBackendInterfaceLocale($locale, $stringToTranslate, $expectedTranslation)
    {
        $this->_login();

        $this->_helper->switchBackendInterfaceLocale($locale);

        $userInterfaceLocale = Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getUser()->getInterfaceLocale();
        $this->assertEquals($userInterfaceLocale, $locale);

        $translationModel = Mage::getSingleton('Mage_Core_Model_Translate');
        $translationLocale = $translationModel->getLocale();
        $this->assertEquals($translationLocale, $locale);

        $translatedString = $translationModel->translate(array($stringToTranslate));
        $this->assertEquals($translatedString, $expectedTranslation);

        $this->_logout();
    }

    /**
     * Test for getUserInterfaceLocale method
     *
     * @covers Mage_Backend_Helper_Data::getUserInterfaceLocale
     */
    public function testGetUserInterfaceLocale()
    {
        $this->_login();

        $locale = $this->_helper->getUserInterfaceLocale();
        $this->assertEquals($locale, Mage_Core_Model_Locale::DEFAULT_LOCALE);

        Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getUser()->setInterfaceLocale('de_DE');

        $locale = $this->_helper->getUserInterfaceLocale();
        $this->assertEquals($locale, 'de_DE');

        $this->_logout();
    }

}
