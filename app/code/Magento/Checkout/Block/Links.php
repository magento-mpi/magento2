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
namespace Magento\Checkout\Block;

class Links extends \Magento\Core\Block\Template
{
    /**
     * Add shopping cart link to parent block
     *
     * @return \Magento\Checkout\Block\Links
     */
    public function addCartLink()
    {
        /** @var $parentBlock \Magento\Page\Block\Template\Links */
        $parentBlock = $this->getParentBlock();
        if ($parentBlock && \Mage::helper('Magento\Core\Helper\Data')->isModuleOutputEnabled('Magento_Checkout')) {
            $count = $this->getSummaryQty() ? $this->getSummaryQty()
                : $this->helper('\Magento\Checkout\Helper\Cart')->getSummaryCount();
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
        if ($parentBlock and $parentBlock instanceof \Magento\Page\Block\Template\Links) {
            $parentBlock->removeLinkByUrl($this->getUrl('checkout/cart'));
        }
    }

    /**
     * Add link on checkout page to parent block
     *
     * @return \Magento\Checkout\Block\Links
     */
    public function addCheckoutLink()
    {
        if (!$this->helper('\Magento\Checkout\Helper\Data')->canOnepageCheckout()) {
            return $this;
        }

        /** @var $parentBlock \Magento\Page\Block\Template\Links */
        $parentBlock = $this->getParentBlock();
        if ($parentBlock && \Mage::helper('Magento\Core\Helper\Data')->isModuleOutputEnabled('Magento_Checkout')) {
            $text = __('Checkout');
            $parentBlock->addLink($text, 'checkout', $text, true, array('_secure' => true), 60, null,
                'class="top-link-checkout"'
            );
        }
        return $this;
    }
}
