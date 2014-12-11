<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftRegistry\Block\Email;

/**
 * Update email template gift registry items block
 */
class Items extends \Magento\Framework\View\Element\Template
{
    /**
     * Return list of gift registry items
     *
     * @return \Magento\GiftRegistry\Model\Resource\Item\Collection
     */
    public function getItems()
    {
        return $this->getEntity()->getItemsCollection();
    }

    /**
     * Count gift registry items in last order
     *
     * @param \Magento\GiftRegistry\Model\Resource\Item $item
     * @return int
     */
    public function getQtyOrdered($item)
    {
        $updatedQty = $this->getEntity()->getUpdatedQty();
        if (is_array($updatedQty) && !empty($updatedQty[$item->getId()]['ordered'])) {
            return $updatedQty[$item->getId()]['ordered'] * 1;
        }
        return 0;
    }

    /**
     * Return gift registry entity remained item qty
     *
     * @param \Magento\GiftRegistry\Model\Resource\Item $item
     * @return int
     */
    public function getRemainedQty($item)
    {
        $qty = ($item->getQty() - $this->getQtyFulfilled($item)) * 1;
        if ($qty > 0) {
            return $qty;
        }
        return 0;
    }

    /**
     * Return gift registry entity item qty
     *
     * @param \Magento\GiftRegistry\Model\Resource\Item $item
     * @return int
     */
    public function getQty($item)
    {
        return $item->getQty() * 1;
    }

    /**
     * Return gift registry entity item fulfilled qty
     *
     * @param \Magento\GiftRegistry\Model\Resource\Item $item
     * @return int
     */
    public function getQtyFulfilled($item)
    {
        $updatedQty = $this->getEntity()->getUpdatedQty();
        if (is_array($updatedQty) && !empty($updatedQty[$item->getId()]['fulfilled'])) {
            return $updatedQty[$item->getId()]['fulfilled'] * 1;
        }
        return $item->getQtyFulfilled() * 1;
    }
}
