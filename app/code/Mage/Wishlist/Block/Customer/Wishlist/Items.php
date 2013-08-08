<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist block customer items
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Block_Customer_Wishlist_Items extends Magento_Core_Block_Template
{
    /**
     * Retreive table column object list
     *
     * @return array
     */
    public function getColumns()
    {
        $columns = array();
        foreach ($this->getLayout()->getChildBlocks($this->getNameInLayout()) as $child) {
            if ($child->isEnabled()){
                $columns[] = $child;
            }
        }
        return $columns;
    }
}
