<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Resource\Order\Invoice;

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
    protected $gridTableName = 'sales_flat_invoice_grid';

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
            ->where(($field ?: 'sfi.entity_id') . ' = ?', $value);
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
            [($field ?: 'sfi.entity_id') . ' = ?' => $value]
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
            ->from(['sfi' => 'sales_flat_invoice'], [])
            ->join(['sfo' => 'sales_flat_order'], 'sfi.order_id = sfo.entity_id', [])
            ->joinLeft(['sba' => 'sales_flat_order_address'], 'sfo.billing_address_id = sba.entity_id', [])
            ->columns(
                [
                    'entity_id' => 'sfi.entity_id',
                    'store_id' => 'sfi.store_id',
                    'base_grand_total' => 'sfi.base_grand_total',
                    'grand_total' => 'sfi.grand_total',
                    'order_id' => 'sfi.order_id',
                    'state' => 'sfi.state',
                    'store_currency_code' => 'sfi.store_currency_code',
                    'order_currency_code' => 'sfi.order_currency_code',
                    'base_currency_code' => 'sfi.base_currency_code',
                    'global_currency_code' => 'sfi.global_currency_code',
                    'increment_id' => 'sfi.increment_id',
                    'order_increment_id' => 'sfo.increment_id',
                    'created_at' => 'sfi.created_at',
                    'order_created_at' => 'sfo.created_at',
                    'billing_name' => "trim(concat(ifnull(sba.firstname, ''), ' ', ifnull(sba.lastname, '')))"
                ]
            );
    }
}
