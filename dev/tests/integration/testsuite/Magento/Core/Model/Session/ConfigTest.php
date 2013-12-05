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

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_model = $objectManager->create('\Magento\Core\Model\Session\Config', array(
            'saveMethod' => 'files',
            'cacheLimiter' => $this->_cacheLimiter
        ));
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testDefaultConfiguration()
    {
        $this->assertEquals(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\Dir')->getDir('session'),
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
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetSessionSaveMethod()
    {
        $this->assertEquals('files', $this->_model->getSaveHandler());
    }
}

