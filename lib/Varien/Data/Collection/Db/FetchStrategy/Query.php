<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Retrieving collection data by querying a database
 */
class Varien_Data_Collection_Db_FetchStrategy_Query implements Varien_Data_Collection_Db_FetchStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function fetchAll(Zend_Db_Select $select, array $bindParams = array())
    {
        return $select->getAdapter()->fetchAll($select, $bindParams);
    }
}
