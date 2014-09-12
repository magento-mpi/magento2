<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource;

use Magento\Framework\Model\Resource\Db\AbstractDb;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\App\Resource as AppResource;

/**
 * Class AbstractGrid
 */
abstract class AbstractGrid extends AbstractDb implements GridInterface
{
    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var string
     */
    protected $gridTableName;

    /**
     * @var string
     */
    protected $orderTableName = 'sales_flat_order';

    /**
     * @var string
     */
    protected $addressTableName = 'sales_flat_order_address';

    /**
     * @param AppResource $resource
     */
    public function __construct(AppResource $resource)
    {
        parent::__construct($resource);
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        //
    }

    /**
     * Returns connection
     *
     * @return AdapterInterface
     */
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->_resources->getConnection('write');
        }
        return $this->connection;
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
            $this->getTable($this->gridTableName),
            [($field ?: 'entity_id') . ' = ?' => $value]
        );
    }
}
