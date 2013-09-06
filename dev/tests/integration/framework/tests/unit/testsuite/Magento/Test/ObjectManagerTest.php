<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_ObjectManager_Test
 */
class Magento_Test_ObjectManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Expected instance manager parametrized cache after clear
     *
     * @var array
     */
    protected $_instanceCache = array(
        'hashShort' => array(),
        'hashLong'  => array()
    );

    public function testClearCache()
    {
        $resource = new stdClass;
        $instanceConfig = new Magento_TestFramework_ObjectManager_Config();
        $primaryConfig = $this->getMock('Magento_Core_Model_Config_Primary', array(), array(), '', false);
        $dirs = $this->getMock('Magento_Core_Model_Dir', array(), array(), '', false);
        $verification = $this->getMock('Magento_Core_Model_Dir_Verification', array(), array(), '', false);
        $cache = $this->getMock('Magento_Core_Model_CacheInterface');
        $configLoader = $this->getMock('Magento_Core_Model_ObjectManager_ConfigLoader', array(), array(), '', false);
        $configLoader->expects($this->once())->method('load')->will($this->returnValue(array()));
        $configCache = $this->getMock('Magento_Core_Model_ObjectManager_ConfigCache', array(), array(), '', false);
        $primaryConfig->expects($this->any())->method('getDirectories')->will($this->returnValue($dirs));
        $primaryLoaderMock = $this->getMock(
            'Magento_Core_Model_ObjectManager_ConfigLoader_Primary', array(), array(), '', false
        );

        $model = new Magento_TestFramework_ObjectManager(
            $primaryConfig, $instanceConfig,
            array(
                'Magento_Core_Model_Dir_Verification' => $verification,
                'Magento_Core_Model_Cache_Type_Config' => $cache,
                'Magento_Core_Model_ObjectManager_ConfigLoader' => $configLoader,
                'Magento_Core_Model_ObjectManager_ConfigCache' => $configCache,
                'Magento_Config_ReaderInterface' => $this->getMock('Magento\Config\ReaderInterface'),
                'Magento_Config_ScopeInterface' => $this->getMock('Magento_Config_ScopeInterface'),
                'Magento_Config_CacheInterface' => $this->getMock('Magento_Config_CacheInterface'),
                'Magento_Cache_FrontendInterface' => $this->getMock('Magento\Cache\FrontendInterface'),
            ),
            $primaryLoaderMock
        );

        $model->addSharedInstance($resource, 'Magento_Core_Model_Resource');
        $instance1 = $model->get('Magento_TestFramework_Request');

        $this->assertSame($instance1, $model->get('Magento_TestFramework_Request'));
        $this->assertSame($model, $model->clearCache());
        $this->assertSame($model, $model->get('Magento\ObjectManager'));
        $this->assertSame($resource, $model->get('Magento_Core_Model_Resource'));
        $this->assertNotSame($instance1, $model->get('Magento_TestFramework_Request'));
    }
}
