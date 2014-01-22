<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Session;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Session\Config
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
        /** @var $sessionManager \Magento\Session\SessionManager */
        $sessionManager = $this->_objectManager->get('Magento\Session\SessionManager');
        if ($sessionManager->isSessionExists()) {
            $sessionManager->destroy();
        }
        $this->_model = $this->_objectManager->create('Magento\Core\Model\Session\Config', array(
            'saveMethod' => 'files',
            'cacheLimiter' => $this->_cacheLimiter
        ));
    }

    protected function tearDown()
    {
        $this->_objectManager->removeSharedInstance('Magento\Core\Model\Session\Config');
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testDefaultConfiguration()
    {
        $this->assertEquals(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\Filesystem')
                ->getPath('session'),
            $this->_model->getSavePath()
        );
        $this->assertEquals(
            \Magento\Core\Model\Session\Config::COOKIE_LIFETIME_DEFAULT,
            $this->_model->getCookieLifetime()
        );
        $this->assertEquals($this->_cacheLimiter, $this->_model->getCacheLimiter());
        $this->assertEquals('/', $this->_model->getCookiePath());
        $this->assertEquals('localhost', $this->_model->getCookieDomain());
        $this->assertEquals(false, $this->_model->getCookieSecure());
        $this->assertEquals(true, $this->_model->getCookieHttpOnly());
        $this->assertEquals($this->_model->getOption('save_path'), ini_get('session.save_path'));
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetSessionSaveMethod()
    {
        $this->assertEquals('files', $this->_model->getSaveHandler());
    }
}
