<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Eav Mssql resource helper model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Helper_Mssql extends Mage_Eav_Model_Resource_Helper_Mssql
{

    /**
     * Returns columns for select prepared for unions
     *
     * @param string $tableAlias
     * @param string $eavType
     * @return array
     */
    public function attributeSelectFields($tableAlias, $eavType)
    {
        return array(
            'value_id',
            'entity_type_id',
            'attribute_id',
            'store_id',
            'entity_id',
            'value' => $this->prepareEavAttributeValue($tableAlias . '.value', $eavType)
        );
    }

    /**
     * Compare Flat style with Describe style columns
     * If column a different - return false
     *
     * @param array $column
     * @param array $describe
     * @return bool
     */
    public function compareIndexColumnProperties($column, $describe)
    {
        $type = $column['type'];
        $length     = null;
        $precision  = null;
        $scale      = null;
        if (isset($column['length'])) {
            $type = sprintf('%s(%s)', $type, $column['length']);
            $length = $column['length'];
        }

        $matches = array();
        if (preg_match('/^((?:var)?char)\((\d+)\)/', $type, $matches)) {
            $type       = $matches[1];
            $length     = $matches[2];
        } else if (preg_match('/^decimal\((\d+),(\d+)\)/', $type, $matches)) {
            $type       = 'decimal';
            $precision  = $matches[1];
            $scale      = $matches[2];
            $length     = 14;
        } else if (preg_match('/^float\((\d+),(\d+)\)/', $type, $matches)) {
            $type       = 'float';
            $precision  = $matches[1];
            $scale      = $matches[2];
        } else if (preg_match('/^((?:big|medium|small|tiny)?int)\((\d+)\)?/', $type, $matches)) {
            $type       = $matches[1];
        } else {
            $type = $column['type'];
        }

        if ($type == 'smallint') {
            $length = 2;
        } elseif ($type == 'text') {
            if ($length > 2147483647) {
                $length = 2147483647;
            }
        }

        $dbColumn = array();
        $newColumn = array();
        $dbColumn['DATA_TYPE'] = $this->getDdlTypeByColumnType($describe['DATA_TYPE']);
        $newColumn['DATA_TYPE'] = $type;
        if ($column['default'] !== false) {
            $dbColumn['DEFAULT'] = $describe['DEFAULT'];
            $newColumn['DEFAULT'] = $column['default'];
        }
        if ($column['nullable'] !== null) {
            $dbColumn['NULLABLE'] = (bool)$describe['NULLABLE'];
            $newColumn['NULLABLE'] = (bool)$column['nullable'];
        }
        if ($length !== null) {
            $dbColumn['LENGTH'] = (int)$describe['LENGTH'];
            $newColumn['LENGTH'] = (int)$length;
        }
        if ($scale !== null) {
            $dbColumn['SCALE'] = (int)$describe['SCALE'];
            $newColumn['SCALE'] = (int)$scale;
        }
        if ($precision !== null) {
            $dbColumn['PRECISION'] = (int)$describe['PRECISION'];
            $newColumn['PRECISION'] = (int)$precision;
        }

        return $dbColumn === $newColumn;
    }

    /**
     * Getting condition isNull(f1,f2) IS NOT Null
     *
     * @param string $field1
     * @param string $field2
     * @return string
     */
    public function getIsNullNotNullCondition($field1, $field2)
    {
        return sprintf('ISNULL(DATALENGTH(%s), 0) + ISNULL(DATALENGTH(%s), 0) > 0', $field1, $field2);
    }
}
