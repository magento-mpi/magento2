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
namespace Magento\Backend\Model\Locale;

/**
 * @magentoAppArea adminhtml
 */
class ResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Locale\ResolverInterface
     */
    protected $_model;

    protected function setUp()
    {
        parent::setUp();
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Backend\Model\Locale\Resolver'
        );
    }

    /**
     * @covers \Magento\Locale\ResolverInterface::setLocale
     */
    public function testSetLocaleWithDefaultLocale()
    {
        $this->_checkSetLocale(\Magento\Locale\ResolverInterface::DEFAULT_LOCALE);
    }

    /**
     * @covers \Magento\Locale\ResolverInterface::setLocale
     */
    public function testSetLocaleWithBaseInterfaceLocale()
    {
        $user = new \Magento\Object();
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Backend\Model\Auth\Session'
        );
        $session->setUser($user);
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Backend\Model\Auth\Session'
        )->getUser()->setInterfaceLocale(
            'fr_FR'
        );
        $this->_checkSetLocale('fr_FR');
    }

    /**
     * @covers \Magento\Locale\ResolverInterface::setLocale
     */
    public function testSetLocaleWithSessionLocale()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Backend\Model\Session'
        )->setSessionLocale(
            'es_ES'
        );
        $this->_checkSetLocale('es_ES');
    }

    /**
     * @covers \Magento\Locale\ResolverInterface::setLocale
     */
    public function testSetLocaleWithRequestLocale()
    {
        $request = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\App\RequestInterface');
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
