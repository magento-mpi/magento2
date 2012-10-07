<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_CacheTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Cache
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Mage_Core_Model_Cache();
    }

    public function tearDown()
    {
        $this->_model = null;
    }

    public function testConstructorBackendDatabase()
    {
        $model = new Mage_Core_Model_Cache(array('backend' => 'Database'));
        $backend = $model->getFrontend()->getBackend();
        $this->assertInstanceOf('Varien_Cache_Backend_Database', $backend);
    }

    /**
     * @param string $optionCode
     * @param string $extensionRequired
     * @dataProvider constructorBackendTwoLevelsDataProvider
     */
    public function testConstructorBackendTwoLevels($optionCode, $extensionRequired)
    {
        if ($extensionRequired) {
            if (!extension_loaded($extensionRequired)) {
                $this->markTestSkipped("The PHP extension '{$extensionRequired}' is required for this test.");

            }
        }
        $model = new Mage_Core_Model_Cache(array('backend' => $optionCode));
        $backend = $model->getFrontend()->getBackend();
        $this->assertInstanceOf('Zend_Cache_Backend_TwoLevels', $backend);
    }

    /**
     * @return array
     */
    public function constructorBackendTwoLevelsDataProvider()
    {
        return array(
            array('Memcached', 'memcached'),
            array('Memcached', 'memcache'),
        );
    }

    public function testGetDbAdapter()
    {
        $this->assertInstanceOf('Zend_Db_Adapter_Abstract', $this->_model->getDbAdapter());
    }
}
