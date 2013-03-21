<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Cache_Frontend_PoolTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Cache_Frontend_Pool
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = Mage::getModel('Mage_Core_Model_Cache_Frontend_Pool');
    }

    public function testDbCacheAdapter()
    {
        $config = Mage::getSingleton('Mage_Core_Model_Config_Primary');
        $frontendNode = $config->getNode(Mage_Core_Model_Cache_Frontend_Pool::XML_PATH_SETTINGS_DEFAULT);
        $frontendOptions = $frontendNode ? $frontendNode->asArray() : array();
        if (
            !((isset($frontendOptions['backend']) && $frontendOptions['backend'] == 'database')
            || (isset($frontendOptions['slow_backend']) && $frontendOptions['slow_backend'] == 'database'))
        ) {
            $this->markTestSkipped('Backend cache adapters is not \'database\'.');
        }

        $cache = $this->_model->get(Mage_Core_Model_Cache_Frontend_Pool::DEFAULT_FRONTEND_ID);
        $this->assertInstanceOf('Magento_Cache_FrontendInterface', $cache);
    }
}
