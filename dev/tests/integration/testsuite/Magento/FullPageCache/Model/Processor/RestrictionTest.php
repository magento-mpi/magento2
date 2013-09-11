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
     * @var \Magento\FullPageCache\Model\Processor
     */
    protected $_model;

    public static function setUpBeforeClass()
    {
        /** @var \Magento\Core\Model\Cache\StateInterface $cacheState */
        $cacheState = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get(
            '\Magento\Core\Model\Cache\StateInterface');
        $cacheState->setEnabled('full_page', true);
    }

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento\FullPageCache\Model\Processor');
    }

    public function testIsAllowedNoCacheCookie()
    {
        $this->assertTrue($this->_model->isAllowed());
        $_COOKIE[\Magento\FullPageCache\Model\Processor\RestrictionInterface::NO_CACHE_COOKIE] = '1';
        $this->assertFalse($this->_model->isAllowed());
    }

    public function testIsAllowedNoCacheGetParam()
    {
        $this->assertTrue($this->_model->isAllowed());
        $_GET['no_cache'] = '1';
        $this->assertFalse($this->_model->isAllowed());
    }
}
