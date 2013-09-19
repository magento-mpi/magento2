<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Resource\Product\Option;

class ValueStub extends \Magento\Catalog\Model\Resource\Product\Option\Value
{
    /**
    * Stub parent constructor
    */
    public function __construct()
    {
        $this->_connections = array(
            'read' =>
            new MysqlStub(),
            'write' =>
            new MysqlStub(),
        );
    }

    /**
     * Save option value price data
     *
     * @param \Magento\Core\Model\AbstractModel $object
     */
    public function saveValueTitles(\Magento\Core\Model\AbstractModel $object)
    {
        $this->_saveValueTitles($object);
    }

    /**
     * We should stub to not use db
     *
     * @param string $tableName
     * @return string
     */
    public function getTable($tableName)
    {
        return $tableName;
    }
}
