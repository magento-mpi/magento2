<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Links block
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Block_Links extends Magento_Core_Block_Template
{
    /**
     * Add shopping cart link to parent block
     *
     * @return Magento_Checkout_Block_Links
     */
    public function addCartLink()
    {
        /** @var $parentBlock Magento_Page_Block_Template_Links */
        $parentBlock = $this->getParentBlock();
        if ($parentBlock && $this->_coreData->isModuleOutputEnabled('Magento_Checkout')) {
            $count = $this->getSummaryQty() ? $this->getSummaryQty()
                : $this->helper('Magento_Checkout_Helper_Cart')->getSummaryCount();
            if ($count == 1) {
                $text = __('My Cart (%1 item)', $count);
            } elseif ($count > 0) {
                $text = __('My Cart (%1 items)', $count);
            } else {
                $text = __('My Cart');
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
        if ($parentBlock and $parentBlock instanceof Magento_Page_Block_Template_Links) {
            $parentBlock->removeLinkByUrl($this->getUrl('checkout/cart'));
        }
    }

    /**
     * Add link on checkout page to parent block
     *
     * @return Magento_Checkout_Block_Links
     */
    public function addCheckoutLink()
    {
        if (!$this->helper('Magento_Checkout_Helper_Data')->canOnepageCheckout()) {
            return $this;
        }

        /** @var $parentBlock Magento_Page_Block_Template_Links */
        $parentBlock = $this->getParentBlock();
        if ($parentBlock && $this->_coreData->isModuleOutputEnabled('Magento_Checkout')) {
            $text = __('Checkout');
            $parentBlock->addLink($text, 'checkout', $text, true, array('_secure' => true), 60, null,
                'class="top-link-checkout"'
            );
        }
        return $this;
    }
}
