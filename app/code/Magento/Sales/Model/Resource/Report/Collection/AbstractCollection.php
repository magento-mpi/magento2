<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Report collection abstract model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Report\Collection;

class AbstractCollection
    extends \Magento\Reports\Model\Resource\Report\Collection\AbstractCollection
{
    /**
     * Order status
     *
     * @var string
     */
    protected $_orderStatus        = null;

    /**
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Sales_Model_Resource_Report $resource
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        \Magento\Sales\Model\Resource\Report $resource
    ) {
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $resource);
        $this->setModel('Magento_Adminhtml_Model_Report_Item');
    }

    /**
     * Set status filter
     *
     * @param string $orderStatus
     * @return \Magento\Sales\Model\Resource\Report\Collection\AbstractCollection
     */
    public function addOrderStatusFilter($orderStatus)
    {
        $this->_orderStatus = $orderStatus;
        return $this;
    }

    /**
     * Apply order status filter
     *
     * @return \Magento\Sales\Model\Resource\Report\Collection\AbstractCollection
     */
    protected function _applyOrderStatusFilter()
    {
        if (is_null($this->_orderStatus)) {
            return $this;
        }
        $orderStatus = $this->_orderStatus;
        if (!is_array($orderStatus)) {
            $orderStatus = array($orderStatus);
        }
        $this->getSelect()->where('order_status IN(?)', $orderStatus);
        return $this;
    }

    /**
     * Order status filter is custom for this collection
     *
     * @return \Magento\Sales\Model\Resource\Report\Collection\AbstractCollection
     */
    protected function _applyCustomFilter()
    {
        return $this->_applyOrderStatusFilter();
    }
}
