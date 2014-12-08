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
namespace Magento\Framework\Data\Collection\Db\FetchStrategy;

class Query implements \Magento\Framework\Data\Collection\Db\FetchStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function fetchAll(\Zend_Db_Select $select, array $bindParams = [])
    {
        return $select->getAdapter()->fetchAll($select, $bindParams);
    }
}
