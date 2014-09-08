<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order;

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
    protected $gridTableName = 'sales_flat_order_grid';

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
            ->where(($field ?: 'sfo.entity_id') . ' = ?', $value);
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
            [($field ?: 'entity_id') . ' = ?' => $value]
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
            ->from(['sfo' => 'sales_flat_order'], [])
            ->joinLeft(['sba' => 'sales_flat_order_address'], 'sfo.billing_address_id = sba.entity_id', [])
            ->joinLeft(['ssa' => 'sales_flat_order_address'], 'sfo.billing_address_id = ssa.entity_id', [])
            ->columns(
                [
                    'entity_id' => 'sfo.entity_id',
                    'status' => 'sfo.status',
                    'store_id' => 'sfo.store_id',
                    'store_name' => 'sfo.store_name',
                    'customer_id' => 'sfo.customer_id',
                    'base_grand_total' => 'sfo.base_grand_total',
                    'base_total_paid' => 'sfo.base_total_paid',
                    'grand_total' => 'sfo.grand_total',
                    'total_paid' => 'sfo.total_paid',
                    'increment_id' => 'sfo.increment_id',
                    'base_currency_code' => 'sfo.base_currency_code',
                    'order_currency_code' => 'sfo.order_currency_code',
                    'shipping_name' => "trim(concat(ifnull(ssa.firstname, ''), ' ' ,ifnull(ssa.lastname, '')))",
                    'billing_name' => "trim(concat(ifnull(sba.firstname, ''), ' ', ifnull(sba.lastname, '')))",
                    'created_at' => 'sfo.created_at',
                    'updated_at' => 'sfo.updated_at'
                ]
            );
    }
}
