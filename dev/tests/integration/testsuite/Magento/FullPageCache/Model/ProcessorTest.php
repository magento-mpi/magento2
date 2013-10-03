<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\FullPageCache\Model\Processor
     */
    protected $_model;

    public static function setUpBeforeClass()
    {
        /** @var $cacheState \Magento\Core\Model\Cache\StateInterface */
        $cacheState = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Core\Model\Cache\StateInterface');
        $cacheState->setEnabled('full_page', true);
    }

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\FullPageCache\Model\Processor');
    }

    public function testIsAllowedHttps()
    {
        $this->assertTrue($this->_model->isAllowed());
        $_SERVER['HTTPS'] = 'on';
        $this->assertFalse($this->_model->isAllowed());
    }

    public function testIsAllowedSessionIdGetParam()
    {
        $this->assertTrue($this->_model->isAllowed());
        $_GET[\Magento\Core\Model\Session\AbstractSession::SESSION_ID_QUERY_PARAM] = 'session_id';
        $this->assertFalse($this->_model->isAllowed());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testIsAllowedUseCacheFlag()
    {
        $this->assertTrue($this->_model->isAllowed());
        /** @var \Magento\Core\Model\Cache\StateInterface $cacheState */
        $cacheState = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\Cache\StateInterface');
        $cacheState->setEnabled('full_page', false);
        $this->assertFalse($this->_model->isAllowed());
    }
}
