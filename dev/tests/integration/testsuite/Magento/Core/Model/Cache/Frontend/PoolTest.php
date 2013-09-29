<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Cache\Frontend;

class PoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Cache\Frontend\Pool
     */
    protected $_model;

    /**
     * @dataProvider cacheBackendDataProvider
     */
    public function testGetCache($cacheBackendName)
    {
        $settings = array('backend' => $cacheBackendName);
        $this->_model = new \Magento\Core\Model\Cache\Frontend\Pool(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Cache\Frontend\Factory'),
            $settings
        );


        $cache = $this->_model->get(\Magento\Core\Model\Cache\Frontend\Pool::DEFAULT_FRONTEND_ID);
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
