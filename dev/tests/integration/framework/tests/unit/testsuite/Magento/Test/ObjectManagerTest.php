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
        $config = $this->getMock('Mage_Core_Model_Config_Primary', array(), array(), '', false);
        $instanceConfig = new Magento_Test_ObjectManager_Config();
        $factory = new Magento_ObjectManager_Factory_Factory($instanceConfig);
        $model = new Magento_Test_ObjectManager($factory, $config, $instanceConfig);
        $model->addSharedInstance($resource, 'Mage_Core_Model_Resource');
        $instance1 = $model->get('Magento_Test_Request');

        $this->assertSame($instance1, $model->get('Magento_Test_Request'));
        $this->assertSame($model, $model->clearCache());
        $this->assertSame($model, $model->get('Magento_ObjectManager'));
        $this->assertSame($resource, $model->get('Mage_Core_Model_Resource'));
        $this->assertNotSame($instance1, $model->get('Magento_Test_Request'));
    }
}
