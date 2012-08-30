<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for resource setup model needed for migration process between Magento versions
 */
class Mage_Core_Model_Resource_Setup_MigrationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Result of update class aliases to compare with expected.
     * Used in callback for Varien_Db_Select::update.
     *
     * @var array
     */
    protected $_actualUpdateResult;

    protected function tearDown()
    {
        unset($this->_actualUpdateResult);
    }

    /**
     * Retrieve all necessary objects mocks which used inside customer storage
     *
     * @param int $tableRowsCount
     * @param array $tableData
     * @param array $aliasesMap
     *
     * @return array
     */
    protected function _getModelDependencies($tableRowsCount = 0, $tableData = array(), $aliasesMap = array())
    {
        $autoload = $this->getMock('Magento_Autoload', array('classExists'), array(), '', false);
        $autoload->expects($this->any())
            ->method('classExists')
            ->will($this->returnCallback(array($this, 'classExistCallback')));

        $selectMock = $this->getMock('Varien_Db_Select', array(), array(), '', false);
        $selectMock->expects($this->any())
                    ->method('from')
                    ->will($this->returnSelf());
        $selectMock->expects($this->any())
                    ->method('where')
                    ->will($this->returnSelf());

        $adapterMock = $this->getMock('Varien_Db_Adapter_Pdo_Mysql',
            array('select', 'update', 'fetchAll', 'fetchOne'), array(), '', false
        );
        $adapterMock->expects($this->any())
            ->method('select')
            ->will($this->returnValue($selectMock));
        $adapterMock->expects($this->any())
            ->method('update')
            ->will($this->returnCallback(array($this, 'updateCallback')));
        $adapterMock->expects($this->any())
            ->method('fetchAll')
            ->will($this->returnValue($tableData));
        $adapterMock->expects($this->any())
            ->method('fetchOne')
            ->will($this->returnValue($tableRowsCount));

        return array(
            'resource_config'   => 'not_used',
            'connection_config' => 'not_used',
            'module_config'     => 'not_used',
            'base_dir'          => 'not_used',
            'path_to_map_file'  => 'not_used',
            'connection'        => $adapterMock,
            'autoload'          => $autoload,
            'core_helper'       => new Mage_Core_Helper_Data(),
            'aliases_map'       => $aliasesMap
        );
    }

    /**
     * Callback for Magento_Autoload::classExist
     *
     * @return bool
     */
    public function classExistCallback()
    {
        return true;
    }

    public function updateCallback($table, array $bind, $where)
    {
        $fields = array_keys($bind);
        $replacements = array_values($bind);
        $aliases = array_values($where);

        $this->_actualUpdateResult[] = array(
            'table' => $table,
            'field' => $fields[0],
            'to' => $replacements[0],
            'from' => $aliases[0]
        );
    }

    public function testAppendClassAliasReplace()
    {
        $setupModel = new Mage_Core_Model_Resource_Setup_Migration('core_setup', $this->_getModelDependencies());

        $setupModel->appendClassAliasReplace(
            'tableName', 'fieldName', 'entityType', 'fieldContentType', 'additionalWhere'
        );

        $expectedRulesList = array (
            'tableName' => array(
                'fieldName' => array(
                    'entity_type'      => 'entityType',
                    'content_type'     => 'fieldContentType',
                    'additional_where' => 'additionalWhere'
                )
            )
        );

        $this->assertAttributeEquals($expectedRulesList, '_replaceRules', $setupModel);
    }

    /**
     * @dataProvider updateClassAliasesDataProvider
     */
    public function testDoUpdateClassAliases($replaceRules, $tableData, $expected, $aliasesMap = array())
    {
        $this->_actualUpdateResult = array();

        $tableRowsCount = count($tableData);

        $setupModel = new Mage_Core_Model_Resource_Setup_Migration(
            'core_setup',
            $this->_getModelDependencies($tableRowsCount, $tableData, $aliasesMap)
        );

        foreach ($replaceRules as $replaceRule) {
            call_user_func_array(array($setupModel, 'appendClassAliasReplace'), $replaceRule);
        }

        $setupModel->doUpdateClassAliases();

        $this->assertEquals($expected, $this->_actualUpdateResult);
    }

    /**
     * Data provider for updating class aliases
     *
     * @return array
     */
    public function updateClassAliasesDataProvider()
    {
        return array(
            'plain text replace' => array(
                '$replaceRules' => array(
                    array(
                        'table',
                        'field',
                        Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
                        Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_PLAIN,
                        ''
                    )
                ),
                '$tableData' => array(
                    array('field' => 'customer/customer'),
                    array('field' => 'customer/attribute_data_postcode'),
                    array('field' => 'Mage_Customer_Model_Customer')
                ),
                '$expected' => array(
                    array(
                        'table' => 'table',
                        'field' => 'field',
                        'to'    => 'Mage_Customer_Model_Customer_FROM_MAP',
                        'from'  => 'customer/customer'
                    ),
                    array(
                        'table' => 'table',
                        'field' => 'field',
                        'to'    => 'Mage_Customer_Model_Attribute_Data_Postcode',
                        'from'  => 'customer/attribute_data_postcode'
                    ),
                ),
                '$aliasesMap' => array(
                    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL => array(
                        'customer/customer' => 'Mage_Customer_Model_Customer_FROM_MAP'
                    )
                )
            ),
        );
    }
}
