<?php
/**
 * Shoping cart model
 *
 * @package     Mage
 * @subpackage  Checkout
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Checkout_Model_Cart extends Varien_Object
{
    /**
     * Retrieve checkout session model
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    /**
     * Retrieve current quote object
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckoutSession()->getQuote();
    }
    
    /**
     * Add products 
     *
     * @param   int $productId
     * @param   int $qty
     * @return  Mage_Checkout_Model_Cart
     */
    public function addProduct($productId, $qty=1)
    {
        $product = Mage::getModel('catalog/product')->load($productId);
        if ($product->getId()) {
            $qty = (int) $qty;
            $qty = $qty>0 ? $qty : 1;
            switch ($product->getTypeId()) {
                case Mage_Catalog_Model_Product::TYPE_SIMPLE:
                    $this->_addSimpleProduct($product, $qty);
                    break;
                case Mage_Catalog_Model_Product::TYPE_CONFIGURABLE_SUPER:
                    $this->_addConfigurableProduct($product, $qty);
                    break;
                case Mage_Catalog_Model_Product::TYPE_GROUPED_SUPER:
                    $this->_addGroupedProduct($product, $qty);
                    break;
                default:
                    Mage::throwException('Indefinite product type');
                    break;
            }
        }
        else {
            Mage::throwException('Product do not exist');
        }
        
        $this->getCheckoutSession()->setLastAddedProductId($product->getId());
        return $this;
    }
    
    /**
     * Adding simple product to shopping cart
     *
     * @param   Mage_Catalog_Model_Product $product
     * @param   int $qty
     * @return  Mage_Checkout_Model_Cart
     */
    protected function _addSimpleProduct(Mage_Catalog_Model_Product $product, $qty)
    {
        if ($this->_checkProductStatus($product)) {
            if ($item = $this->getQuote()->getItemByProductId($product->getId())) {
                $itemQty = $item->getQty();
                if ($itemQty+$qty > $product->getQty()) {
                    $this->getCheckoutSession()->setRedirectUrl($product->getProductUrl());
                    Mage::throwException('Requestet quantity do not available');
                }
                else {
                    $product->setQuoteQty($itemQty+$qty);
                }
            }
            else {
                if ($qty > $product->getQty()) {
                    Mage::throwException('Requestet quantity do not available');
                    $this->getCheckoutSession()->setRedirectUrl($product->getProductUrl());
                }
                else {
                    $product->setQuoteQty($qty);
                }
            }
            
            $this->getQuote()->addCatalogProduct($product);
        }
        return $this;
    }
    
    /**
     * Adding grouped product to cart
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Checkout_Model_Cart
     */
    protected function _addGroupedProduct(Mage_Catalog_Model_Product $product)
    {
		/*$superGroupProducts = $this->getRequest()->getParam('super_group', array());
		if(!is_array($superGroupProducts)) {
			$superGroupProducts = array();
		}

		if(sizeof($superGroupProducts)==0) {
		    Mage::getSingleton('catalog/session')->addError('Specify products, please');
			$this->_backToProduct($product->getId());
			return;
		}

		foreach($product->getSuperGroupProductsLoaded() as $superProductLink) {

			if(isset($superGroupProducts[$superProductLink->getLinkedProductId()]) && $qty =  $intFilter->filter($superGroupProducts[$superProductLink->getLinkedProductId()])) {
				   $superProduct = Mage::getModel('catalog/product')
    				->load($superProductLink->getLinkedProductId())
    				->setParentProduct($product);
    			if($superProduct->getId()) {
    				$this->getQuote()->addCatalogProduct($superProduct->setQty($qty));
    			}
			}
		}*/
        return $this;
    }
    
    /**
     * Adding configurable product
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Checkout_Model_Cart
     */
    protected function _addConfigurableProduct(Mage_Catalog_Model_Product $product)
    {
		/*$productId = $product->getSuperLinkIdByOptions($this->getRequest()->getParam('super_attribute'));
		if($productId) {
			$superProduct = Mage::getModel('catalog/product')
				->load($productId)
				->setParentProduct($product);
			if($superProduct->getId()) {
				$item = $this->getQuote()->addCatalogProduct($superProduct->setQty($qty));
				$item->setDescription(
            		$this->getLayout()->createBlock('checkout/cart_item_super')->setSuperProduct($superProduct)->toHtml()
            	);
            	$item->setName($product->getName());
            	$this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
            	$this->getQuote()->save();

			}
		} else {
		    Mage::getSingleton('catalog/session')->addError('Specify product option, please');
			$this->_backToProduct($product->getId());
			return;
		}*/
        return $this;
    }
    
    /**
     * Checking product status
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  bool
     */
    protected function _checkProductStatus(Mage_Catalog_Model_Product $product)
    {
        if ($product->getStatus() == Mage_Catalog_Model_Product::STATUS_ENABLED) {
            return true;
        }
        return false;
    }

    public function addAdditionalProducts($productIds)
    {
        
    }
    
    
    public function removeProduct()
    {
        
    }
    
    public function save()
    {
        $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        $this->getQuote()->save();
        $this->getCheckoutSession()->setQuoteId($this->getQuote()->getId());
        return $this;
    }
}
