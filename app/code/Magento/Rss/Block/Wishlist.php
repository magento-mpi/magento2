<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Shared Wishlist Rss Block
 *
 * @category   Magento
 * @package    Magento_Rss
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rss_Block_Wishlist extends Magento_Wishlist_Block_Abstract
{
    /**
     * Customer instance
     *
     * @var Magento_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_rss';

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Wishlist_Helper_Data $wishlistData
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Customer_Model_Session $customerSession,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Wishlist_Helper_Data $wishlistData,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct($coreRegistry, $wishlistData, $taxData, $catalogData, $coreData, $context, $data);
    }

    /**
     * Retrieve Wishlist model
     *
     * @return Magento_Wishlist_Model_Wishlist
     */
    protected function _getWishlist()
    {
        if (is_null($this->_wishlist)) {
            $this->_wishlist = Mage::getModel('Magento_Wishlist_Model_Wishlist');
            $wishlistId = $this->getRequest()->getParam('wishlist_id');
            if ($wishlistId) {
                $this->_wishlist->load($wishlistId);
                if ($this->_wishlist->getCustomerId() != $this->_getCustomer()->getId()) {
                    $this->_wishlist->unsetData();
                }
            } else {
                if ($this->_getCustomer()->getId()) {
                    $this->_wishlist->loadByCustomer($this->_getCustomer());
                }
            }
        }
        return $this->_wishlist;
    }

    /**
     * Retrieve Customer instance
     *
     * @return Magento_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        if (is_null($this->_customer)) {
            $this->_customer = Mage::getModel('Magento_Customer_Model_Customer');

            $params = $this->_coreData->urlDecode($this->getRequest()->getParam('data'));
            $data   = explode(',', $params);
            $cId    = abs(intval($data[0]));
            if ($cId && ($cId == $this->_customerSession->getCustomerId()) ) {
                $this->_customer->load($cId);
            }
        }

        return $this->_customer;
    }

    /**
     * Build wishlist rss feed title
     *
     * @return string
     */
    protected function _getTitle()
    {
        return __('%1\'s Wishlist', $this->_getCustomer()->getName());
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        /* @var $rssObj Magento_Rss_Model_Rss */
        $rssObj = Mage::getModel('Magento_Rss_Model_Rss');

        if ($this->_getWishlist()->getId()) {
            $newUrl = Mage::getUrl('wishlist/shared/index', array(
                'code'  => $this->_getWishlist()->getSharingCode()
            ));

            $title  = $this->_getTitle();
            $lang   = $this->_storeConfig->getConfig('general/locale/code');

            $rssObj->_addHeader(array(
                'title'         => $title,
                'description'   => $title,
                'link'          => $newUrl,
                'charset'       => 'UTF-8',
                'language'      => $lang
            ));

            /** @var $wishlistItem Magento_Wishlist_Model_Item*/
            foreach ($this->getWishlistItems() as $wishlistItem) {
                /* @var $product Magento_Catalog_Model_Product */
                $product = $wishlistItem->getProduct();
                $productUrl = $this->getProductUrl($product);
                $product->setAllowedInRss(true);
                $product->setAllowedPriceInRss(true);
                $product->setProductUrl($productUrl);
                $args = array('product' => $product);

                $this->_eventManager->dispatch('rss_wishlist_xml_callback', $args);

                if (!$product->getAllowedInRss()) {
                    continue;
                }

                $description = '<table><tr><td><a href="' . $productUrl . '"><img src="'
                    . $this->helper('Magento_Catalog_Helper_Image')->init($product, 'thumbnail')->resize(75, 75)
                    . '" border="0" align="left" height="75" width="75"></a></td>'
                    . '<td style="text-decoration:none;">'
                    . $this->helper('Magento_Catalog_Helper_Output')
                        ->productAttribute($product, $product->getShortDescription(), 'short_description')
                    . '<p>';

                if ($product->getAllowedPriceInRss()) {
                    $description .= $this->getPriceHtml($product, true);
                }
                $description .= '</p>';
                if ($this->hasDescription($product)) {
                    $description .= '<p>' . __('Comment:')
                        . ' ' . $this->helper('Magento_Catalog_Helper_Output')
                            ->productAttribute($product, $product->getDescription(), 'description')
                        . '<p>';
                }

                $description .= '</td></tr></table>';

                $rssObj->_addEntry(array(
                    'title'         => $this->helper('Magento_Catalog_Helper_Output')
                        ->productAttribute($product, $product->getName(), 'name'),
                    'link'          => $productUrl,
                    'description'   => $description,
                ));
            }
        } else {
            $rssObj->_addHeader(array(
                'title'         => __('We cannot retrieve the wish list.'),
                'description'   => __('We cannot retrieve the wish list.'),
                'link'          => Mage::getUrl(),
                'charset'       => 'UTF-8',
            ));
        }

        return $rssObj->createRssXml();
    }

    /**
     * Retrieve Product View URL
     *
     * @param Magento_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getProductUrl($product, $additional = array())
    {
        $additional['_rss'] = true;
        return parent::getProductUrl($product, $additional);
    }

    /**
     * Adding customized price template for product type, used as action in layouts
     *
     * @param string $type Catalog Product Type
     * @param string $block Block Type
     * @param string $template Template
     */
    public function addPriceBlockType($type, $block = '', $template = '')
    {
        if ($type) {
            $this->_priceBlockTypes[$type] = array(
                'block' => $block,
                'template' => $template
            );
        }
    }
}
