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
 * Shopping cart block
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Block_Cart extends Magento_Checkout_Block_Cart_Abstract
{
    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Catalog_Model_Resource_Url
     */
    protected $_catalogUrlBuilder;

    /**
     * @var Magento_Core_Model_UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Catalog_Model_Resource_Url $catalogUrlBuilder
     * @param Magento_Core_Model_UrlInterface $urlBuilder
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Customer_Model_Session $customerSession,
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Catalog_Model_Resource_Url $catalogUrlBuilder,
        Magento_Core_Model_UrlInterface $urlBuilder,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_catalogUrlBuilder = $catalogUrlBuilder;
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($catalogData, $coreData, $context, $customerSession, $checkoutSession, $data);
    }

    /**
     * Prepare Quote Item Product URLs
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->prepareItemUrls();
    }

    /**
     * prepare cart items URLs
     */
    public function prepareItemUrls()
    {
        $products = array();
        /* @var $item Magento_Sales_Model_Quote_Item */
        foreach ($this->getItems() as $item) {
            $product    = $item->getProduct();
            $option     = $item->getOptionByCode('product_type');
            if ($option) {
                $product = $option->getProduct();
            }

            if ($item->getStoreId() != $this->_storeManager->getStore()->getId()
                && !$item->getRedirectUrl()
                && !$product->isVisibleInSiteVisibility())
            {
                $products[$product->getId()] = $item->getStoreId();
            }
        }

        if ($products) {
            $products = $this->_catalogUrlBuilder->getRewriteByProductStore($products);
            foreach ($this->getItems() as $item) {
                $product    = $item->getProduct();
                $option     = $item->getOptionByCode('product_type');
                if ($option) {
                    $product = $option->getProduct();
                }

                if (isset($products[$product->getId()])) {
                    $object = new Magento_Object($products[$product->getId()]);
                    $item->getProduct()->setUrlDataObject($object);
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return $this->getQuote()->getHasError();
    }

    /**
     * @return int
     */
    public function getItemsSummaryQty()
    {
        return $this->getQuote()->getItemsSummaryQty();
    }

    /**
     * @return bool
     */
    public function isWishlistActive()
    {
        $isActive = $this->_getData('is_wishlist_active');
        if ($isActive === null) {
            $isActive = $this->_storeConfig->getConfig('wishlist/general/active')
                && $this->_customerSession->isLoggedIn();
            $this->setIsWishlistActive($isActive);
        }
        return $isActive;
    }

    /**
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->getUrl('checkout/onepage', array('_secure'=>true));
    }

    /**
     * @return mixed
     */
    public function getContinueShoppingUrl()
    {
        $url = $this->getData('continue_shopping_url');
        if (is_null($url)) {
            $url = $this->_checkoutSession->getContinueShoppingUrl(true);
            if (!$url) {
                $url = $this->_urlBuilder->getUrl();
            }
            $this->setData('continue_shopping_url', $url);
        }
        return $url;
    }

    /**
     * @return bool
     */
    public function getIsVirtual()
    {
        return $this->helper('Magento_Checkout_Helper_Cart')->getIsVirtualQuote();
    }

    /**
     * Return list of available checkout methods
     *
     * @param string $alias Container block alias in layout
     * @return array
     */
    public function getMethods($alias)
    {
        $childName = $this->getLayout()->getChildName($this->getNameInLayout(), $alias);
        if ($childName) {
            return $this->getLayout()->getChildNames($childName);
        }
        return array();
    }

    /**
     * Return HTML of checkout method (link, button etc.)
     *
     * @param string $name Block name in layout
     * @return string
     * @throws Magento_Core_Exception
     */
    public function getMethodHtml($name)
    {
        $block = $this->getLayout()->getBlock($name);
        if (!$block) {
            throw new Magento_Core_Exception(__('Invalid method: %1', $name));
        }
        return $block->toHtml();
    }

    /**
     * Return customer quote items
     *
     * @return array
     */
    public function getItems()
    {
        if ($this->getCustomItems()) {
            return $this->getCustomItems();
        }

        return parent::getItems();
    }

    /**
     * @return int
     */
    public function getItemsCount()
    {
        return $this->getQuote()->getItemsCount();
    }
}
