<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Flat sales order abstract comments collection, used as parent for: invoice, shipment, creditmemo
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Sales_Model_Resource_Order_Comment_Collection_Abstract
    extends Mage_Sales_Model_Resource_Collection_Abstract
{
    /**
     * Set filter on comments by their parent item
     *
     * @param Magento_Core_Model_Abstract|int $parent
     * @return Mage_Sales_Model_Resource_Order_Comment_Collection_Abstract
     */
    public function setParentFilter($parent)
    {
        if ($parent instanceof Magento_Core_Model_Abstract) {
            $parent = $parent->getId();
        }
        return $this->addFieldToFilter('parent_id', $parent);
    }

    /**
     * Adds filter to get only 'visible on front' comments
     *
     * @param int $flag
     * @return Mage_Sales_Model_Resource_Order_Comment_Collection_Abstract
     */
    public function addVisibleOnFrontFilter($flag = 1)
    {
        return $this->addFieldToFilter('is_visible_on_front', $flag);
    }

    /**
     * Set created_at sort order
     *
     * @param string $direction
     * @return Mage_Sales_Model_Resource_Order_Comment_Collection_Abstract
     */
    public function setCreatedAtOrder($direction = 'desc')
    {
        return $this->setOrder('created_at', $direction);
    }
}
