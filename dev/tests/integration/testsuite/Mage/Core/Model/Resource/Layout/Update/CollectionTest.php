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
class Mage_Core_Model_Resource_Layout_Update_CollectionTest extends PHPUnit_Framework_TestCase
{
    private static $_layoutModelId;

    /**
     * @var Mage_Core_Model_Resource_Layout_Update_Collection
     */
    protected $_collection;

    protected function setUp()
    {
        $this->_collection = new Mage_Core_Model_Resource_Layout_Update_Collection;
    }

    protected function tearDown()
    {
        $this->_collection = null;
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
     * @dataProvider contextDataProvider
     * @magentoDbIsolation enabled
     */
    public function testLayoutContext($contextData, $filterData)
    {
        foreach ($contextData as $context) {
            $model = new Mage_Core_Model_Layout_Context();
            $model->setData(array(
                'layout_update_id'  => self::$_layoutModelId,
                'entity_name'       => $context['entity_name'],
                'entity_type'       => $context['entity_type'],
                'value_' . $context['entity_type'] => $context['value_' . $context['entity_type']],
                'relation_count'    => $context['relation_count'],
                'relation_hash'     => $context['relation_hash'],
            ))->save();
        }
        $this->assertEquals(1, $this->_collection->addContextFilter($filterData)->count());
    }

    public function contextDataProvider()
    {
        $relationHash = md5(uniqid());
        $relationHashSecond = md5(uniqid());
        return array(
            'contexts_array' => array(
                'context_data' => array(
                    array(
                        'entity_name'       => 'category_title',
                        'entity_type'       => 'varchar',
                        'value_varchar'     => 'my test category',
                        'relation_count'    => 2,
                        'relation_hash'     => $relationHash,
                    ),
                    array(
                        'entity_name'       => 'store_id',
                        'entity_type'       => 'int',
                        'value_int'         => 2,
                        'relation_count'    => 2,
                        'relation_hash'     => $relationHash,
                    ),
                    array(
                        'entity_name'       => 'category_title',
                        'entity_type'       => 'varchar',
                        'value_varchar'     => 'my test category',
                        'relation_count'    => 2,
                        'relation_hash'     => $relationHashSecond,
                    ),
                    array(
                        'entity_name'       => 'store_id',
                        'entity_type'       => 'int',
                        'value_int'         => 1,
                        'relation_count'    => 2,
                        'relation_hash'     => $relationHashSecond,
                    ),
                ),
                'filter_data' => array(
                    array(
                        'condition_entity_name' => 'store_id',
                        'entity_type' => 'int',
                        'condition_entity_value' => array('eq' => 1),
                    ),
                    array(
                        'condition_entity_name' => 'category_title',
                        'entity_type' => 'varchar',
                        'condition_entity_value' => array('eq' => 'my test category'),
                    )
                )
            )
        );
    }
}
