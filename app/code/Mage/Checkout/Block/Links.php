<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Links block
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Block_Links extends Mage_Core_Block_Template
{
    /**
     * Add shopping cart link to parent block
     *
     * @return Mage_Checkout_Block_Links
     */
    public function addCartLink()
    {
        /** @var $parentBlock Mage_Page_Block_Template_Links */
        $parentBlock = $this->getParentBlock();
        if ($parentBlock && Mage::helper('Mage_Core_Helper_Data')->isModuleOutputEnabled('Mage_Checkout')) {
            $count = $this->getSummaryQty() ? $this->getSummaryQty()
                : $this->helper('Mage_Checkout_Helper_Cart')->getSummaryCount();
            if ($count == 1) {
                $text = $this->__('My Cart (%s item)', $count);
            } elseif ($count > 0) {
                $text = $this->__('My Cart (%s items)', $count);
            } else {
                $text = $this->__('My Cart');
            }

            $this->removeParentCartLink();
            $parentBlock->addLink($text, 'checkout/cart', $text, true, array(), 50, null, 'class="top-link-cart"');
        }
        return $this;
    }

    /**
     * Remove checkout/cart link from parent block
     */
    public function removeParentCartLink()
    {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock and $parentBlock instanceof Mage_Page_Block_Template_Links) {
            $parentBlock->removeLinkByUrl($this->getUrl('checkout/cart'));
        }
    }

    /**
     * Add link on checkout page to parent block
     *
     * @return Mage_Checkout_Block_Links
     */
    public function addCheckoutLink()
    {
        if (!$this->helper('Mage_Checkout_Helper_Data')->canOnepageCheckout()) {
            return $this;
        }

        /** @var $parentBlock Mage_Page_Block_Template_Links */
        $parentBlock = $this->getParentBlock();
        if ($parentBlock && Mage::helper('Mage_Core_Helper_Data')->isModuleOutputEnabled('Mage_Checkout')) {
            $text = $this->__('Checkout');
            $parentBlock->addLink($text, 'checkout', $text, true, array('_secure' => true), 60, null,
                'class="top-link-checkout"'
            );
        }
        return $this;
    }
}
