<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Launcher test controller
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Controller_Test extends Mage_Core_Controller_Varien_Action
{
    /**
     * Add product to cart if needed and redirect to 'checkout/cart' page
     *
     * @return null
     */
    public function transactionAction()
    {
        $cart = $this->_objectManager->create('Mage_Checkout_Model_Cart');
        $productModel = $this->_objectManager->create('Mage_Catalog_Model_Product');
        if (!$cart->getQuote()->getItemsCount()) {
            $products = $productModel->getResourceCollection();
            if ($products->count()) {
                $productId = $products->addAttributeToSort('entity_id', 'ASC')->getFirstItem()->getId();
                $product = $productModel->load($productId);
                $cart->addProduct($product, 1);
                $cart->save();
                $this->_objectManager->create('Mage_Checkout_Model_Session')->setCartWasUpdated(true);
            }
        }
        $this->_redirect('checkout/cart');
    }
}
