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
 * Wishlist block customer item column
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Block_Customer_Wishlist_Item_Column extends Mage_Wishlist_Block_Abstract
{
    /**
     * Checks whether column should be shown in table
     *
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * Retrieve block html
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->isEnabled()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Retrieve child blocks html
     *
     * @param string $name
     * @param Mage_Core_Block_Abstract $child
     * @return string
     */
    protected function _beforeChildToHtml($name, $child)
    {
        $child->setItem($this->getItem());
    }

    /**
     * Retrieve column related javascript code
     *
     * @return string
     */
    public function getJs()
    {
        $js = '';
        foreach ($this->getSortedChildBlocks() as $child) {
            $js .= $child->getJs();
        }
        return $js;
    }
}
