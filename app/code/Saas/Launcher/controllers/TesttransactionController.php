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
 * Launcher test transaction controller
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_TesttransactionController extends Mage_Core_Controller_Front_Action
{
    /**
     * Add product to cart if needed and redirect to Shopping Cart page
     *
     * @return null
     */
    public function indexAction()
    {
        $cart = $this->_objectManager->create('Mage_Checkout_Model_Cart');
        if (!$cart->getQuote()->getItemsCount()) {
            $productModel = $this->_objectManager->create('Mage_Catalog_Model_Product');
            $products = $productModel->getResourceCollection()
                ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
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
