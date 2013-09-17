<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist block customer items
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Wishlist_Block_Customer_Wishlist_Items extends Magento_Core_Block_Template
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
