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
 * Sales orders statuses option array
 */
namespace Magento\Sales\Model\Resource\Order\Grid;

class StatusesArray implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var \Magento\Sales\Model\Resource\Order\Status\CollectionFactory
     */
    protected $_statusCollectionFactory;

    /**
     * @param \Magento\Sales\Model\Resource\Order\Status\CollectionFactory $statusCollectionFactory
     */
    public function __construct(\Magento\Sales\Model\Resource\Order\Status\CollectionFactory $statusCollectionFactory)
    {
        $this->_statusCollectionFactory = $statusCollectionFactory;
    }

    /**
     * Return option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $statuses = $this->_statusCollectionFactory->create()->toOptionHash();
        return $statuses;
    }
}
