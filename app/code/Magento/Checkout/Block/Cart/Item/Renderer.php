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
 * Shopping cart item render block
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method Magento_Checkout_Block_Cart_Item_Renderer setProductName(string)
 * @method Magento_Checkout_Block_Cart_Item_Renderer setDeleteUrl(string)
 */
class Magento_Checkout_Block_Cart_Item_Renderer extends Magento_Core_Block_Template
{
    /** @var Magento_Checkout_Model_Session */
    protected $_checkoutSession;
    protected $_item;
    protected $_productUrl = null;
    protected $_productThumbnail = null;

    /**
     * Whether qty will be converted to number
     *
     * @var bool
     */
    protected $_strictQtyMode = true;

    /**
     * Check, whether product URL rendering should be ignored
     *
     * @var bool
     */
    protected $_ignoreProductUrl = false;

    /**
     * Set item for render
     *
     * @param   Magento_Sales_Model_Quote_Item $item
     * @return  Magento_Checkout_Block_Cart_Item_Renderer
     */
    public function setItem(Magento_Sales_Model_Quote_Item_Abstract $item)
    {
        $this->_item = $item;
        return $this;
    }

    /**
     * Get quote item
     *
     * @return Magento_Sales_Model_Quote_Item
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     * Get item product
     *
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->getItem()->getProduct();
    }

    public function overrideProductThumbnail($productThumbnail)
    {
        $this->_productThumbnail = $productThumbnail;
        return $this;
    }

    /**
     * Thumbnail image getter
     *
     * @return Magento_Catalog_Helper_Image
     */
    protected function _getThumbnail()
    {
        if (is_null($this->_productThumbnail)) {
            $product = $this->getProduct();
            if ($this->getProduct()->isConfigurable()) {
                $children = $this->getItem()->getChildren();
                if (isset($children[0])
                    && $children[0]->getProduct()->getThumbnail()
                    && $children[0]->getProduct()->getThumbnail() != 'no_selection'
                ) {
                    $product = $children[0]->getProduct();
                }
            }
            $thumbnail = $this->helper('Magento_Catalog_Helper_Image')->init($product, 'thumbnail');
        } else {
            $thumbnail = $this->_productThumbnail;
        }
        return $thumbnail;
    }

    /**
     * Get product thumbnail image url
     *
     * @return Magento_Catalog_Model_Product_Image
     */
    public function getProductThumbnailUrl()
    {
        return (string) $this->_getThumbnail()->resize($this->getThumbnailSize());
    }

    /**
     * Product image thumbnail getter
     *
     * @return int
     */
    public function getThumbnailSize()
    {
        return $this->getVar('product_thumbnail_image_size', 'Magento_Catalog');
    }

    /**
     * Get product thumbnail image url for sidebar
     *
     * @return Magento_Catalog_Model_Product_Image
     */
    public function getProductThumbnailSidebarUrl()
    {
        return (string) $this->_getThumbnail()->resize($this->getThumbnailSidebarSize())->setWatermarkSize('30x10');
    }

    /**
     * Product image thumbnail getter
     *
     * @return int
     */
    public function getThumbnailSidebarSize()
    {
        return $this->getVar('product_thumbnail_image_sidebar_size', 'Magento_Catalog');
    }

    public function overrideProductUrl($productUrl)
    {
        $this->_productUrl = $productUrl;
        return $this;
    }

    /**
     * Check Product has URL
     *
     * @return bool
     */
    public function hasProductUrl()
    {
        if ($this->_ignoreProductUrl) {
            return false;
        }

        if ($this->_productUrl || $this->getItem()->getRedirectUrl()) {
            return true;
        }

        $product = $this->getProduct();
        $option  = $this->getItem()->getOptionByCode('product_type');
        if ($option) {
            $product = $option->getProduct();
        }

        if ($product->isVisibleInSiteVisibility()) {
            return true;
        }
        else {
            if ($product->hasUrlDataObject()) {
                $data = $product->getUrlDataObject();
                if (in_array($data->getVisibility(), $product->getVisibleInSiteVisibilities())) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Retrieve URL to item Product
     *
     * @return string
     */
    public function getProductUrl()
    {
        if (!is_null($this->_productUrl)) {
            return $this->_productUrl;
        }
        if ($this->getItem()->getRedirectUrl()) {
            return $this->getItem()->getRedirectUrl();
        }

        $product = $this->getProduct();
        $option  = $this->getItem()->getOptionByCode('product_type');
        if ($option) {
            $product = $option->getProduct();
        }

        return $product->getUrlModel()->getUrl($product);
    }

    /**
     * Get item product name
     *
     * @return string
     */
    public function getProductName()
    {
        if ($this->hasProductName()) {
            return $this->getData('product_name');
        }
        return $this->getProduct()->getName();
    }

    /**
     * Get product customize options
     *
     * @return array || false
     */
    public function getProductOptions()
    {
        /* @var $helper Magento_Catalog_Helper_Product_Configuration */
        $helper = Mage::helper('Magento_Catalog_Helper_Product_Configuration');
        return $helper->getCustomOptions($this->getItem());
    }

    /**
     * Get list of all otions for product
     *
     * @return array
     */
    public function getOptionList()
    {
        return $this->getProductOptions();
    }

    /**
     * Get item configure url
     *
     * @return string
     */
    public function getConfigureUrl()
    {
        return $this->getUrl(
            'checkout/cart/configure',
            array('id' => $this->getItem()->getId())
        );
    }

    /**
     * Get item delete url
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        if ($this->hasDeleteUrl()) {
            return $this->getData('delete_url');
        }

        $encodedUrl = $this->helper('Magento_Core_Helper_Url')->getEncodedUrl();
        return $this->getUrl(
            'checkout/cart/delete',
            array(
                'id'=>$this->getItem()->getId(),
                Magento_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $encodedUrl
            )
        );
    }

    /**
     * Get quote item qty
     *
     * @return float|int|string
     */
    public function getQty()
    {
        if (!$this->_strictQtyMode && (string)$this->getItem()->getQty() == '') {
            return '';
        }
        return $this->getItem()->getQty() * 1;
    }

    /**
     * Get checkout session
     *
     * @return Magento_Checkout_Model_Session
     */
    public function getCheckoutSession()
    {
        if (null === $this->_checkoutSession) {
            $this->_checkoutSession = Mage::getSingleton('Magento_Checkout_Model_Session');
        }
        return $this->_checkoutSession;
    }

    /**
     * Retrieve item messages
     * Return array with keys
     *
     * text => the message text
     * type => type of a message
     *
     * @return array
     */
    public function getMessages()
    {
        $messages = array();
        $quoteItem = $this->getItem();

        // Add basic messages occuring during this page load
        $baseMessages = $quoteItem->getMessage(false);
        if ($baseMessages) {
            foreach ($baseMessages as $message) {
                $messages[] = array(
                    'text' => $message,
                    'type' => $quoteItem->getHasError() ? 'error' : 'notice'
                );
            }
        }

        // Add messages saved previously in checkout session
        $checkoutSession = $this->getCheckoutSession();
        if ($checkoutSession) {
            /* @var $collection Magento_Core_Model_Message_Collection */
            $collection = $checkoutSession->getQuoteItemMessages($quoteItem->getId(), true);
            if ($collection) {
                $additionalMessages = $collection->getItems();
                foreach ($additionalMessages as $message) {
                    /* @var $message Magento_Core_Model_Message_Abstract */
                    $messages[] = array(
                        'text' => $message->getCode(),
                        'type' => ($message->getType() == Magento_Core_Model_Message::ERROR) ? 'error' : 'notice'
                    );
                }
            }
        }

        return $messages;
    }

    /**
     * Accept option value and return its formatted view
     *
     * @param mixed $optionValue
     * Method works well with these $optionValue format:
     *      1. String
     *      2. Indexed array e.g. array(val1, val2, ...)
     *      3. Associative array, containing additional option info, including option value, e.g.
     *          array
     *          (
     *              [label] => ...,
     *              [value] => ...,
     *              [print_value] => ...,
     *              [option_id] => ...,
     *              [option_type] => ...,
     *              [custom_view] =>...,
     *          )
     *
     * @return array
     */
    public function getFormatedOptionValue($optionValue)
    {
        /* @var $helper Magento_Catalog_Helper_Product_Configuration */
        $helper = Mage::helper('Magento_Catalog_Helper_Product_Configuration');
        $params = array(
            'max_length' => 55,
            'cut_replacer' => ' <a href="#" class="dots" onclick="return false">...</a>'
        );
        return $helper->getFormattedOptionValue($optionValue, $params);
    }

    /**
     * Check whether Product is visible in site
     *
     * @return bool
     */
    public function isProductVisible()
    {
        return $this->getProduct()->isVisibleInSiteVisibility();
    }

    /**
     * Return product additional information block
     *
     * @return Magento_Core_Block_Abstract
     */
    public function getProductAdditionalInformationBlock()
    {
        return $this->getLayout()->getBlock('additional.product.info');
    }

    /**
     * Get html for MAP product enabled
     *
     * @param Magento_Sales_Model_Quote_Item $item
     * @return string
     */
    public function getMsrpHtml($item)
    {
        return $this->getLayout()->createBlock('Magento_Catalog_Block_Product_Price')
            ->setTemplate('product/price_msrp_item.phtml')
            ->setProduct($item->getProduct())
            ->toHtml();
    }

    /**
     * Set qty mode to be strict or not
     *
     * @param bool $strict
     * @return Magento_Checkout_Block_Cart_Item_Renderer
     */
    public function setQtyMode($strict)
    {
        $this->_strictQtyMode = $strict;
        return $this;
    }

    /**
     * Set ignore product URL rendering
     *
     * @param bool $ignore
     * @return Magento_Checkout_Block_Cart_Item_Renderer
     */
    public function setIgnoreProductUrl($ignore = true)
    {
        $this->_ignoreProductUrl = $ignore;
        return $this;
    }
}
