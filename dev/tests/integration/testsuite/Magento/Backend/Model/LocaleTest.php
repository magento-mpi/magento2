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
namespace Magento\Backend\Model;

class LocaleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_model;

    public function setUp()
    {
        parent::setUp();
        $this->_model = \Mage::getModel('Magento\Backend\Model\Locale');
    }

    /**
     * @covers \Magento\Core\Model\LocaleInterface::setLocale
     */
    public function testSetLocaleWithDefaultLocale()
    {
        $this->_checkSetLocale(\Magento\Core\Model\LocaleInterface::DEFAULT_LOCALE);
    }

    /**
     * @covers \Magento\Core\Model\LocaleInterface::setLocale
     */
    public function testSetLocaleWithBaseInterfaceLocale()
    {
        $user = new \Magento\Object();
        $session = \Mage::getSingleton('Magento\Backend\Model\Auth\Session');
        $session->setUser($user);
        \Mage::getSingleton('Magento\Backend\Model\Auth\Session')->getUser()->setInterfaceLocale('fr_FR');
        $this->_checkSetLocale('fr_FR');
    }

    /**
     * @covers \Magento\Core\Model\LocaleInterface::setLocale
     */
    public function testSetLocaleWithSessionLocale()
    {
        \Mage::getSingleton('Magento\Backend\Model\Session')->setSessionLocale('es_ES');
        $this->_checkSetLocale('es_ES');
    }

    /**
     * @covers \Magento\Core\Model\LocaleInterface::setLocale
     */
    public function testSetLocaleWithRequestLocale()
    {
        $request = \Mage::app()->getRequest();
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
