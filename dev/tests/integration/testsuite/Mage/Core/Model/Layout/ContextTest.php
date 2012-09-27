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

/**
 * @magentoDataFixture layoutDataFixture
 */
class Mage_Core_Model_Layout_ContextTest extends PHPUnit_Framework_TestCase
{
    private static $_layoutModelId;

    /**
     * @var Mage_Core_Model_Layout_Context
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Core_Model_Layout_Context();
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public static function layoutDataFixture()
    {
        $model = new Mage_Core_Model_Layout_Update();
        $model->setData(array(
            'handle'     => 'default',
            'xml'        => '<layout/>',
            'sort_order' => 123,
        ));
        $model->save();
        self::$_layoutModelId = $model->getId();
    }

    /**
     * Test crud operations for layout update context model using valid data
     */
    public function testCrud()
    {
        $this->_model->setData(array(
            'layout_update_id'  => self::$_layoutModelId,
            'entity_name'       => 'store_id',
            'entity_type'       => 'int',
            'value_int'         => 1,
            'relation_count'    => 1,
            'relation_hash'     => md5(uniqid()),
        ));

        $crud = new Magento_Test_Entity($this->_model, array('value_int' => 2));
        $crud->testCrud();
    }

    /**
     * Test unique entity type in relation
     *
     * @expectedException Zend_Db_Statement_Exception
     * @magentoDbIsolation enabled
     */
    public function testInvalidData()
    {
        $model = new Mage_Core_Model_Layout_Context();
        $count = $model->getCollection()->count();
        $relationHash = md5(uniqid());
        $model->setData(array(
            'layout_update_id'  => self::$_layoutModelId,
            'entity_name'       => 'store_id',
            'entity_type'       => 'int',
            'value_int'         => 1,
            'relation_count'    => 2,
            'relation_hash'     => $relationHash,
        ))->save();

        $this->assertEquals($count + 1, $model->getCollection()->count());

        $model = new Mage_Core_Model_Layout_Context();
        $model->setData(array(
            'layout_update_id'  => self::$_layoutModelId,
            'entity_name'       => 'store_id',
            'entity_type'       => 'int',
            'value_int'         => 2,
            'relation_count'    => 2,
            'relation_hash'     => $relationHash,
        ))->save();
    }
}
