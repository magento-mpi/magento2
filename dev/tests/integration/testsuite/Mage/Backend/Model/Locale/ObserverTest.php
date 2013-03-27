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

class Mage_Backend_Model_Locale_ObserverTest extends Mage_Backend_Area_TestCase
{
    /**
     * @var Mage_Backend_Model_Locale_Observer
     */
    protected $_model;

    public function setUp()
    {
        parent::setUp();
        $this->_model = Mage::getModel('Mage_Backend_Model_Locale_Observer');
    }

    /**
     * @covers Mage_Backend_Model_Locale_Observer::bindLocale
     */
    public function testBindLocaleWithDefaultLocale()
    {
        $observer = $this->_getObserverInstanceForBindLocaleTest();
        $this->_checkSetLocale($observer, Mage_Core_Model_LocaleInterface::DEFAULT_LOCALE);
    }

    /**
     * @covers Mage_Backend_Model_Locale_Observer::bindLocale
     */
    public function testBindLocaleWithBaseInterfaceLocale()
    {
        $observer = $this->_getObserverInstanceForBindLocaleTest();
        $user = new Varien_Object();
        $session = Mage::getSingleton('Mage_Backend_Model_Auth_Session');
        $session->setUser($user);
        Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getUser()->setInterfaceLocale('fr_FR');
        $this->_checkSetLocale($observer, 'fr_FR');
    }

    /**
     * @covers Mage_Backend_Model_Locale_Observer::bindLocale
     */
    public function testBindLocaleWithSessionLocale()
    {
        $observer = $this->_getObserverInstanceForBindLocaleTest();
        Mage::getSingleton('Mage_Backend_Model_Session')->setSessionLocale('es_ES');
        $this->_checkSetLocale($observer, 'es_ES');
    }

    /**
     * @covers Mage_Backend_Model_Locale_Observer::bindLocale
     */
    public function testBindLocaleWithRequestLocale()
    {
        $observer = $this->_getObserverInstanceForBindLocaleTest();
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
    protected function _getObserverInstanceForBindLocaleTest()
    {
        $observer = new Varien_Event_Observer();
        $event = new Varien_Event();
        $observer->setEvent($event);
        $observer->getEvent()
            ->setLocale(Mage::getSingleton('Mage_Core_Model_LocaleInterface'));

        return $observer;
    }
}
