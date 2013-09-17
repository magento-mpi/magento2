<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Update email template gift registry items block
 */
class Magento_GiftRegistry_Block_Email_Items extends Magento_Core_Block_Template
{

    /**
     * Return list of gift registry items
     *
     * @return Magento_GiftRegistry_Model_Resource_Item_Collection
     */
    public function getItems()
    {
        return $this->getEntity()->getItemsCollection();
    }

    /**
     * Count gift registry items in last order
     *
     * @param Magento_GiftRegistry_Model_Resource_Item $item
     * @return mixed
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
     * @param Magento_GiftRegistry_Model_Resource_Item $item
     * @return mixed
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
     * @param Magento_GiftRegistry_Model_Resource_Item $item
     * @return mixed
     */
    public function getQty($item)
    {
        return $item->getQty() * 1;
    }

    /**
     * Return gift registry entity item fulfilled qty
     *
     * @param Magento_GiftRegistry_Model_Resource_Item $item
     * @return mixed
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
