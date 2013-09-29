<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Eav Mysql resource helper model
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Resource;

class Helper extends \Magento\Eav\Model\Resource\Helper
{
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
        if (isset($column['length'])) {
            $type = sprintf('%s(%s)', $type[0], $column['length']);
        } else {
            $type = $type[0];
        }
        $length     = null;
        $precision  = null;
        $scale      = null;

        $matches = array();
        if (preg_match('/^((?:var)?char)\((\d+)\)/', $type, $matches)) {
            $type       = $matches[1];
            $length     = $matches[2];
        } else if (preg_match('/^decimal\((\d+),(\d+)\)/', $type, $matches)) {
            $type       = 'decimal';
            $precision  = $matches[1];
            $scale      = $matches[2];
        } else if (preg_match('/^float\((\d+),(\d+)\)/', $type, $matches)) {
            $type       = 'float';
            $precision  = $matches[1];
            $scale      = $matches[2];
        } else if (preg_match('/^((?:big|medium|small|tiny)?int)\((\d+)\)?/', $type, $matches)) {
            $type       = $matches[1];
        }

        return ($describe['DATA_TYPE'] == $type)
            && ($describe['DEFAULT'] == $column['default'])
            && ((bool)$describe['NULLABLE'] == (bool)$column['nullable'])
            && ((bool)$describe['UNSIGNED'] == (bool)$column['unsigned'])
            && ($describe['LENGTH'] == $length)
            && ($describe['SCALE'] == $scale)
            && ($describe['PRECISION'] == $precision);
    }

    /**
     * Getting condition isNull(f1,f2) IS NOT Null
     *
     * @param string $firstField
     * @param string $secondField
     * @return string
     */
    public function getIsNullNotNullCondition($firstField, $secondField)
    {
        return sprintf('%s IS NOT NULL', $this->_getReadAdapter()->getIfNullSql($firstField, $secondField));
    }
}
