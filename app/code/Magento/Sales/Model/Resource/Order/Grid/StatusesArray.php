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
    protected $_statusCollFactory;

    /**
     * @param \Magento\Sales\Model\Resource\Order\Status\CollectionFactory $statusCollFactory
     */
    public function __construct(\Magento\Sales\Model\Resource\Order\Status\CollectionFactory $statusCollFactory)
    {
        $this->_statusCollFactory = $statusCollFactory;
    }

    /**
     * Return option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $statuses = $this->_statusCollFactory->create()->toOptionHash();
        return $statuses;
    }
}
