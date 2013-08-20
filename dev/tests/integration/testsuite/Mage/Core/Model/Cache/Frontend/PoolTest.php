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

    /**
     * @dataProvider cacheBackendDataProvider
     */
    public function testGetCache($cacheBackendName)
    {
        $settings = array('backend' => $cacheBackendName);
        $this->_model = new Mage_Core_Model_Cache_Frontend_Pool(
            Mage::getModel('Mage_Core_Model_Cache_Frontend_Factory'),
            $settings
        );


        $cache = $this->_model->get(Mage_Core_Model_Cache_Frontend_Pool::DEFAULT_FRONTEND_ID);
        $this->assertInstanceOf('Magento_Cache_FrontendInterface', $cache);
        $this->assertInstanceOf('Zend_Cache_Backend_Interface', $cache->getBackend());
    }

    public function cacheBackendDataProvider()
    {
        return array(
            array('sqlite'),
            array('memcached'),
            array('apc'),
            array('xcache'),
            array('eaccelerator'),
            array('database'),
            array('File'),
            array('')
        );
    }
}
