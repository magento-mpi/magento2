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

class Mage_Backend_Model_ObserverTest extends Mage_Backend_Area_TestCase
{
    /**
     * @var Mage_Backend_Model_Observer
     */
    protected $_model;

    public function setUp()
    {
        parent::setUp();
        $this->_model = Mage::getModel('Mage_Backend_Model_Observer');
    }

    public function testActionPreDispatchAdminNotLogged()
    {
        $this->markTestSkipped('Skipped because of authentication process moved into base controller.');

        $request = Mage::app()->getRequest();
        $this->assertEmpty($request->getRouteName());
        $this->assertEmpty($request->getControllerName());
        $this->assertEmpty($request->getActionName());

        $observer = $this->_buildObserver();
        $this->_model->actionPreDispatchAdmin($observer);

        $this->assertEquals('adminhtml', $request->getRouteName());
        $this->assertEquals('auth', $request->getControllerName());
        $this->assertEquals('login', $request->getActionName());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testActionPreDispatchAdminLoggedRedirect()
    {
        $this->markTestSkipped('Skipped because of authentication process moved into base controller.');

        $observer = $this->_buildObserver();
        $this->_model->actionPreDispatchAdmin($observer);

        $response = Mage::app()->getResponse();
        $code = $response->getHttpResponseCode();
        $this->assertTrue($code >= 300 && $code < 400);

        $session = Mage::getSingleton('Mage_Backend_Model_Auth_Session');
        $this->assertTrue($session->isLoggedIn());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store admin/security/use_form_key 0
     */
    public function testActionPreDispatchAdminLoggedNoRedirect()
    {
        $this->markTestSkipped('Skipped because of authentication process moved into base controller.');

        $observer = $this->_buildObserver();
        $this->_model->actionPreDispatchAdmin($observer);

        $response = Mage::app()->getResponse();
        $code = $response->getHttpResponseCode();
        $this->assertFalse($code >= 300 && $code < 400);

        $session = Mage::getSingleton('Mage_Backend_Model_Auth_Session');
        $this->assertTrue($session->isLoggedIn());
    }

    /**
     * Builds a dummy observer for testing adminPreDispatch method
     *
     * @return Varien_Object
     */
    protected function _buildObserver()
    {
        $request = Mage::app()->getRequest();
        $request->setPost(
            'login',
            array(
                'username' => Magento_Test_Bootstrap::ADMIN_NAME,
                'password' => Magento_Test_Bootstrap::ADMIN_PASSWORD
            )
        );

        $controller = new Varien_Object(array('request' => $request));
        $event = new Varien_Object(array('controller_action' => $controller));
        $observer = new Varien_Object(array('event' => $event));
        return $observer;
    }

    /**
     * Test for bindLocale method
     * check default locale is applied
     *
     * @covers Mage_Backend_Model_Observer::bindLocale
     */
    public function testBindLocaleDefault()
    {
        $observer = $this->_createObserverBindLocale();
        $this->_checkSetLocale($observer, Mage_Core_Model_Locale::DEFAULT_LOCALE);
    }

    /**
     * Test for bindLocale method:
     * check user interface locale (from db) is applied
     *
     * @covers Mage_Backend_Model_Observer::bindLocale
     */
    public function testBindLocaleBaseInterfaceLocale()
    {
        $observer = $this->_createObserverBindLocale();
        $user = new Varien_Object();
        $session = Mage::getSingleton('Mage_Backend_Model_Auth_Session');
        $session->setUser($user);
        Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getUser()->setInterfaceLocale('fr_FR');
        $this->_checkSetLocale($observer, 'fr_FR');
    }

    /**
     * Test for bindLocale method:
     * check session locale (previously set through get param) is applied
     *
     * @covers Mage_Backend_Model_Observer::bindLocale
     */
    public function testBindLocaleSessionLocale()
    {
        $observer = $this->_createObserverBindLocale();
        Mage::getSingleton('Mage_Backend_Model_Session')->setSessionLocale('es_ES');
        $this->_checkSetLocale($observer, 'es_ES');
    }

    /**
     * Test for bindLocale method:
     * check current arrived (through get param) locale is applied
     *
     * @covers Mage_Backend_Model_Observer::bindLocale
     */
    public function testBindLocaleRequestLocale()
    {
        $observer = $this->_createObserverBindLocale();
        $request = Mage::app()->getRequest();
        $request->setPost(array('locale' => 'de_DE'));
        $this->_checkSetLocale($observer, 'de_DE');
    }

    /**
     * Check set locale
     *
     * @param Varien_Event_Observer $observer
     * @param string $localeCodeToCheck
     * @return void
     */
    protected function _checkSetLocale($observer, $localeCodeToCheck)
    {
        $this->_model->bindLocale($observer);
        $localeCode = $observer->getEvent()->getLocale()->getLocaleCode();
        $this->assertEquals($localeCode, $localeCodeToCheck);
    }

    /**
     * Create empty observer for bindLocale tests
     *
     * @return Varien_Event_Observer
     */
    protected function _createObserverBindLocale()
    {
        $observer = new Varien_Event_Observer();
        $event = new Varien_Event();
        $observer->setEvent($event);
        $observer->getEvent()
            ->setLocale(Mage::getSingleton('Mage_Core_Model_Locale'));

        return $observer;
    }
}
