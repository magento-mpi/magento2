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
 * Flat sales order abstract comments collection, used as parent for: invoice, shipment, creditmemo
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Order\Comment\Collection;

abstract class AbstractCollection
    extends \Magento\Sales\Model\Resource\Collection\AbstractCollection
{
    /**
     * Set filter on comments by their parent item
     *
     * @param \Magento\Core\Model\AbstractModel|int $parent
     * @return \Magento\Sales\Model\Resource\Order\Comment\Collection\AbstractCollection
     */
    public function setParentFilter($parent)
    {
        if ($parent instanceof \Magento\Core\Model\AbstractModel) {
            $parent = $parent->getId();
        }
        return $this->addFieldToFilter('parent_id', $parent);
    }

    /**
     * Adds filter to get only 'visible on front' comments
     *
     * @param int $flag
     * @return \Magento\Sales\Model\Resource\Order\Comment\Collection\AbstractCollection
     */
    public function addVisibleOnFrontFilter($flag = 1)
    {
        return $this->addFieldToFilter('is_visible_on_front', $flag);
    }

    /**
     * Set created_at sort order
     *
     * @param string $direction
     * @return \Magento\Sales\Model\Resource\Order\Comment\Collection\AbstractCollection
     */
    public function setCreatedAtOrder($direction = 'desc')
    {
        return $this->setOrder('created_at', $direction);
    }
}
