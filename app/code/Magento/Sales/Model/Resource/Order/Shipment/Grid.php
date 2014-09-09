<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Resource\Order\Shipment;

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
    protected $gridTableName = 'sales_flat_shipment_grid';

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
            ->where(($field ?: 'sfs.entity_id') . ' = ?', $value);
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
            [($field ?: 'sfs.entity_id') . ' = ?' => $value]
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
            ->from(['sfs' => 'sales_flat_shipment'], [])
            ->join(['sfo' => 'sales_flat_order'], 'sfs.order_id = sfo.entity_id', [])
            ->joinLeft(['ssa' => 'sales_flat_order_address'], 'sfo.billing_address_id = ssa.entity_id', [])
            ->columns(
                [
                    'entity_id' => 'sfs.entity_id',
                    'store_id' => 'sfs.store_id',
                    'total_qty' => 'sfs.total_qty',
                    'order_id' => 'sfs.order_id',
                    'shipment_status' => 'sfs.shipment_status',
                    'increment_id' => 'sfs.increment_id',
                    'order_increment_id' => 'sfo.increment_id',
                    'created_at' => 'sfs.created_at',
                    'order_created_at' => 'sfo.created_at',
                    'shipping_name' => "trim(concat(ifnull(ssa.firstname, ''), ' ' ,ifnull(ssa.lastname, '')))",
                ]
            );
    }
}
