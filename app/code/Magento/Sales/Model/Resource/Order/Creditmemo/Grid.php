<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Resource\Order\Creditmemo;

use Magento\Sales\Model\Resource\GridInterface;
use Magento\Framework\App\Resource;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Class Grid
 */
class Grid implements GridInterface
{
    /**
     * @var \Magento\Framework\App\Resource
     */
    protected $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var string
     */
    protected $gridTableName = 'sales_flat_creditmemo_grid';

    /**
     * @param Resource $resource
     */
    public function __construct(
        Resource $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * Returns grid table name
     *
     * @return string
     */
    public function getGridTableName()
    {
        return $this->gridTableName;
    }

    /**
     * Returns connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->resource->getConnection('write');
        }
        return $this->connection;
    }

    /**
     * Refresh grid row
     *
     * @param int|string $value
     * @param null|string $field
     * @return \Zend_Db_Statement_Interface
     */
    public function refresh($value, $field = null)
    {
        $select = $this->getGridOriginSelect()
            ->where(($field ?: 'sfc.entity_id') . ' = ?', $value);
        return $this->getConnection()->query(
            $this->getConnection()
                ->insertFromSelect($select, $this->gridTableName, [], AdapterInterface::INSERT_ON_DUPLICATE)
        );
    }

    /**
     * Purge grid row
     *
     * @param int|string $value
     * @param null|string $field
     * @return int
     */
    public function purge($value, $field = null)
    {
        return $this->getConnection()->delete(
            $this->getGridTableName(),
            [($field ?: 'sfc.entity_id') . ' = ?' => $value]
        );
    }

    /**
     * Returns select object
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function getGridOriginSelect()
    {
        return $this->getConnection()->select()
            ->from(['sfc' => 'sales_flat_creditmemo'], [])
            ->join(['sfo' => 'sales_flat_order'], 'sfc.order_id = sfo.entity_id', [])
            ->joinLeft(['sba' => 'sales_flat_order_address'], 'sfo.billing_address_id = sba.entity_id', [])
            ->columns(
                [
                    'entity_id' => 'sfc.entity_id',
                    'store_id' => 'sfc.store_id',
                    'store_to_order_rate' => 'sfc.store_to_order_rate',
                    'base_to_order_rate' => 'sfc.base_to_order_rate',
                    'grand_total' => 'sfc.grand_total',
                    'store_to_base_rate' => 'sfc.store_to_base_rate',
                    'base_to_global_rate' => 'sfc.base_to_global_rate',
                    'base_grand_total' => 'sfc.base_grand_total',
                    'order_id' => 'sfc.order_id',
                    'creditmemo_status' => 'sfc.creditmemo_status',
                    'state' => 'sfc.state',
                    'invoice_id' => 'sfc.invoice_id',
                    'store_currency_code' => 'sfc.store_currency_code',
                    'order_currency_code' => 'sfc.order_currency_code',
                    'base_currency_code' => 'sfc.base_currency_code',
                    'global_currency_code' => 'sfc.global_currency_code',
                    'increment_id' => 'sfc.increment_id',
                    'order_increment_id' => 'sfo.increment_id',
                    'created_at' => 'sfc.created_at',
                    'order_created_at' => 'sfo.created_at',
                    'billing_name' => "trim(concat(ifnull(sba.firstname, ''), ' ', ifnull(sba.lastname, '')))"
                ]
            );
    }
}
