<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Model\Resource\Plugin;

/**
 * Plugin 'sales-archive-move-to-active' for order grid refresh
 */
class Grid
{
    /**
     * @var \Magento\Sales\Model\Resource\GridPool
     */
    protected $gridPool;
    /**
     * @var \Magento\SalesArchive\Model\Resource\Archive
     */
    protected $archive;

    /**
     * @param \Magento\Sales\Model\Resource\GridPool $gridPool
     * @param \Magento\SalesArchive\Model\Resource\Archive $archive
     */
    public function __construct(
        \Magento\Sales\Model\Resource\GridPool $gridPool,
        \Magento\SalesArchive\Model\Resource\Archive $archive
    ) {
        $this->gridPool = $gridPool;
        $this->archive = $archive;
    }

    /**
     * Removes order from archive and refreshes grids
     *
     * @param \Magento\Sales\Model\Resource\Order\Grid $grid
     * @param \Closure $proceed
     * @param string $value
     * @param string|null $field
     * @return \Magento\Sales\Model\Resource\GridPool | \Zend_Db_Statement_Interface
     */
    public function aroundRefresh(
        \Magento\Sales\Model\Resource\Order\Grid $grid,
        \Closure $proceed,
        $value,
        $field = null
    ) {
        if ($this->archive->isOrderInArchive($value)) {
            $this->archive->removeOrdersFromArchiveById([$value]);
            return $this->gridPool->refreshByOrderId($value);
        }

        return $proceed($value, $field);
    }
}
