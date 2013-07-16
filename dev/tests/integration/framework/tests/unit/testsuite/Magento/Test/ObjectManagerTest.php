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
        $instanceConfig = new Magento_Test_ObjectManager_Config();
        $primaryConfig = $this->getMock('Mage_Core_Model_Config_Primary', array(), array(), '', false);
        $dirs = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);
        $verification = $this->getMock('Mage_Core_Model_Dir_Verification', array(), array(), '', false);
        $cache = $this->getMock('Mage_Core_Model_CacheInterface');
        $configLoader = $this->getMock('Mage_Core_Model_ObjectManager_ConfigLoader', array(), array(), '', false);
        $primaryConfig->expects($this->any())->method('getDirectories')->will($this->returnValue($dirs));
        $model = new Magento_Test_ObjectManager($primaryConfig, $instanceConfig, array(
            'Mage_Core_Model_Dir_Verification' => $verification,
            'Mage_Core_Model_Cache_Type_Config' => $cache,
            'Mage_Core_Model_ObjectManager_ConfigLoader' => $configLoader
        ));
        $model->addSharedInstance($resource, 'Mage_Core_Model_Resource');
        $instance1 = $model->get('Magento_Test_Request');

        $this->assertSame($instance1, $model->get('Magento_Test_Request'));
        $this->assertSame($model, $model->clearCache());
        $this->assertSame($model, $model->get('Magento_ObjectManager'));
        $this->assertSame($resource, $model->get('Mage_Core_Model_Resource'));
        $this->assertNotSame($instance1, $model->get('Magento_Test_Request'));
    }
}
