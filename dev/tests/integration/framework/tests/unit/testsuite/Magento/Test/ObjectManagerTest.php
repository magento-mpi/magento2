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
        $config = $this->getMock('Magento_ObjectManager_Configuration');
        $model = new Magento_Test_ObjectManager($config, '');
        $model->addSharedInstance($resource, 'Mage_Core_Model_Resource');
        $instance1 = $model->get('Magento_Test_Request');

        $this->assertSame($instance1, $model->get('Magento_Test_Request'));
        $this->assertSame($model, $model->clearCache());
        $this->assertSame($model, $model->get('Magento_ObjectManager'));
        $this->assertSame($resource, $model->get('Mage_Core_Model_Resource'));
        $this->assertNotSame($instance1, $model->get('Magento_Test_Request'));
    }
}
