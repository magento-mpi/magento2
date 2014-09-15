<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Session;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Session\Config
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_cacheLimiter = 'private_no_expire';

    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $sessionManager \Magento\Framework\Session\SessionManager */
        $sessionManager = $this->_objectManager->get('Magento\Framework\Session\SessionManager');
        if ($sessionManager->isSessionExists()) {
            $sessionManager->writeClose();
        }
        $this->_model = $this->_objectManager->create(
            'Magento\Framework\Session\Config',
            array('saveMethod' => 'files', 'cacheLimiter' => $this->_cacheLimiter)
        );
    }

    protected function tearDown()
    {
        $this->_objectManager->removeSharedInstance('Magento\Framework\Session\Config');
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testDefaultConfiguration()
    {
        $this->assertEquals(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                'Magento\Framework\App\Filesystem'
            )->getPath(
                'session'
            ),
            $this->_model->getSavePath()
        );
        $this->assertEquals(
            \Magento\Framework\Session\Config::COOKIE_LIFETIME_DEFAULT,
            $this->_model->getCookieLifetime()
        );
        $this->assertEquals($this->_cacheLimiter, $this->_model->getCacheLimiter());
        $this->assertEquals('/', $this->_model->getCookiePath());
        $this->assertEquals('localhost', $this->_model->getCookieDomain());
        $this->assertEquals(false, $this->_model->getCookieSecure());
        $this->assertEquals(true, $this->_model->getCookieHttpOnly());
        $this->assertEquals($this->_model->getSavePath(), $this->_model->getOption('save_path'));
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetSessionSaveMethod()
    {
        $this->assertEquals('files', $this->_model->getSaveHandler());
    }

    /**
     * Unable to add integration tests for testGetLifetimePathNonDefault
     *
     * Error: Cannot modify header information - headers already sent
     */
    public function testGetLifetimePathNonDefault()
    {

    }
}
