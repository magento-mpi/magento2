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
 * Wishlist block customer item cart column
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Block_Customer_Wishlist_Item_Column_Comment
    extends Mage_Wishlist_Block_Customer_Wishlist_Item_Column
{
    /**
     * Retrieve column javascript code
     *
     * @return string
     */
    public function getJs()
    {
        return parent::getJs() . "
        function focusComment(obj) {
            if( obj.value == '" . $this->helper('Mage_Wishlist_Helper_Data')->defaultCommentString() . "' ) {
                obj.value = '';
            } else if( obj.value == '' ) {
                obj.value = '" . $this->helper('Mage_Wishlist_Helper_Data')->defaultCommentString() . "';
            }
        }
        ";
    }
}
