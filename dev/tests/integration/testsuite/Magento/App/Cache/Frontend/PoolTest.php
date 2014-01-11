<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Cache\Frontend;

class PoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Cache\Frontend\Pool
     */
    protected $_model;

    /**
     * @dataProvider cacheBackendDataProvider
     */
    public function testGetCache($cacheBackendName)
    {
        $settings = array('backend' => $cacheBackendName);
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\App\Cache\Frontend\Pool', array('defaultSettings' => $settings))
        ;
        $cache = $this->_model->get(\Magento\App\Cache\Frontend\Pool::DEFAULT_FRONTEND_ID);
        $this->assertInstanceOf('Magento\Cache\FrontendInterface', $cache);
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
