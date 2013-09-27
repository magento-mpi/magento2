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
class Magento_FullPageCache_Model_Processor_RestrictionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_FullPageCache_Model_Processor
     */
    protected $_model;

    public static function setUpBeforeClass()
    {
        /** @var Magento_Core_Model_Cache_StateInterface $cacheState */
        $cacheState = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get(
            'Magento_Core_Model_Cache_StateInterface');
        $cacheState->setEnabled('full_page', true);
    }

    protected function setUp()
    {
        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_FullPageCache_Model_Processor');
    }

    public function testIsAllowedNoCacheCookie()
    {
        $this->assertTrue($this->_model->isAllowed());
        $_COOKIE[Magento_FullPageCache_Model_Processor_RestrictionInterface::NO_CACHE_COOKIE] = '1';
        $this->assertFalse($this->_model->isAllowed());
    }

    public function testIsAllowedNoCacheGetParam()
    {
        $this->assertTrue($this->_model->isAllowed());
        $_GET['no_cache'] = '1';
        $this->assertFalse($this->_model->isAllowed());
    }
}
