<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Backend_Model_LocaleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_model;

    protected function setUp()
    {
        parent::setUp();
        $this->_model = Mage::getModel('Magento_Backend_Model_Locale');
    }

    /**
     * @covers Magento_Core_Model_LocaleInterface::setLocale
     */
    public function testSetLocaleWithDefaultLocale()
    {
        $this->_checkSetLocale(Magento_Core_Model_LocaleInterface::DEFAULT_LOCALE);
    }

    /**
     * @covers Magento_Core_Model_LocaleInterface::setLocale
     */
    public function testSetLocaleWithBaseInterfaceLocale()
    {
        $user = new Magento_Object();
        $session = Mage::getSingleton('Magento_Backend_Model_Auth_Session');
        $session->setUser($user);
        Mage::getSingleton('Magento_Backend_Model_Auth_Session')->getUser()->setInterfaceLocale('fr_FR');
        $this->_checkSetLocale('fr_FR');
    }

    /**
     * @covers Magento_Core_Model_LocaleInterface::setLocale
     */
    public function testSetLocaleWithSessionLocale()
    {
        Mage::getSingleton('Magento_Backend_Model_Session')->setSessionLocale('es_ES');
        $this->_checkSetLocale('es_ES');
    }

    /**
     * @covers Magento_Core_Model_LocaleInterface::setLocale
     */
    public function testSetLocaleWithRequestLocale()
    {
        $request = Mage::app()->getRequest();
        $request->setPost(array('locale' => 'de_DE'));
        $this->_checkSetLocale('de_DE');
    }

    /**
     * Check set locale
     *
     * @param string $localeCodeToCheck
     * @return void
     */
    protected function _checkSetLocale($localeCodeToCheck)
    {
        $this->_model->setLocale();
        $localeCode = $this->_model->getLocaleCode();
        $this->assertEquals($localeCode, $localeCodeToCheck);
    }
}
