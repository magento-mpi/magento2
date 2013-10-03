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
namespace Magento\Data\Collection\Db\FetchStrategy;

class Query implements \Magento\Data\Collection\Db\FetchStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function fetchAll(\Zend_Db_Select $select, array $bindParams = array())
    {
        return $select->getAdapter()->fetchAll($select, $bindParams);
    }
}
