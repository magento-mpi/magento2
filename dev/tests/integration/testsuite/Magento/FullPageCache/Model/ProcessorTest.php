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
class Magento_FullPageCache_Model_ProcessorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_FullPageCache_Model_Processor
     */
    protected $_model;

    public static function setUpBeforeClass()
    {
        /** @var $cacheState Magento_Core_Model_Cache_StateInterface */
        $cacheState = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get(
            'Magento_Core_Model_Cache_StateInterface');
        $cacheState->setEnabled('full_page', true);
    }

    protected function setUp()
    {
        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_FullPageCache_Model_Processor');
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
        $_GET[Magento_Core_Model_Session_Abstract::SESSION_ID_QUERY_PARAM] = 'session_id';
        $this->assertFalse($this->_model->isAllowed());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testIsAllowedUseCacheFlag()
    {
        $this->assertTrue($this->_model->isAllowed());
        /** @var Magento_Core_Model_Cache_StateInterface $cacheState */
        $cacheState = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_Cache_StateInterface');
        $cacheState->setEnabled('full_page', false);
        $this->assertFalse($this->_model->isAllowed());
    }
}
