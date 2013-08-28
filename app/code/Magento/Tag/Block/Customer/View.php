<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * List of products tagged by customer Block
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Block_Customer_View extends Magento_Catalog_Block_Product_Abstract
{
    /**
     * Tagged Product Collection
     *
     * @var Magento_Tag_Model_Resource_Product_Collection
     */
    protected $_collection;

    /**
     * Current Tag object
     *
     * @var Magento_Tag_Model_Tag
     */
    protected $_tagInfo;

    public function __construct(Magento_Catalog_Helper_Image $catalogImage, Magento_Page_Helper_Layout $pageLayout, Magento_Catalog_Helper_Product_Compare $catalogProductCompare, Magento_Wishlist_Helper_Data $wishlistData, Magento_Checkout_Helper_Cart $checkoutCart, Magento_Tax_Helper_Data $taxData, Magento_Catalog_Helper_Data $catalogData, Magento_Core_Helper_Data $coreData, Magento_Core_Block_Template_Context $context, array $data = array())
    {
        parent::__construct($catalogImage, $pageLayout, $catalogProductCompare, $wishlistData, $checkoutCart, $taxData, $catalogData, $coreData, $context, $data);
    }

    /**
     * Initialize block
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTagId(Mage::registry('tagId'));
    }

    /**
     * Retrieve current Tag object
     *
     * @return Magento_Tag_Model_Tag
     */
    public function getTagInfo()
    {
        if (is_null($this->_tagInfo)) {
            $this->_tagInfo = Mage::getModel('Magento_Tag_Model_Tag')
                ->load($this->getTagId());
        }
        return $this->_tagInfo;
    }

    /**
     * Retrieve Tagged Product Collection items
     *
     * @return array
     */
    public function getMyProducts()
    {
        return $this->_getCollection()->getItems();
    }

    /**
     * Retrieve count of Tagged Product(s)
     *
     * @return int
     */
    public function getCount()
    {
        return sizeof($this->getMyProducts());
    }

    /**
     * Retrieve Product Info URL
     *
     * @param int $productId
     * @return string
     */
    public function getReviewUrl($productId)
    {
        return Mage::getUrl('review/product/list', array('id' => $productId));
    }

    /**
     * Preparing block layout
     *
     * @return Magento_Tag_Block_Customer_View
     */
    protected function _prepareLayout()
    {
        $toolbar = $this->getLayout()
            ->createBlock('Magento_Page_Block_Html_Pager', 'customer_tag_list.toolbar')
            ->setCollection($this->_getCollection());

        $this->setChild('toolbar', $toolbar);
        return parent::_prepareLayout();
    }

    /**
     * Retrieve Toolbar block HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Retrieve Current Mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getChildBlock('toolbar')->getCurrentMode();
    }

    /**
     * Retrieve Tagged product(s) collection
     *
     * @return Magento_Tag_Model_Resource_Product_Collection
     */
    protected function _getCollection()
    {
        if (is_null($this->_collection)) {
            $this->_collection = Mage::getModel('Magento_Tag_Model_Tag')
                ->getEntityCollection()
                ->addTagFilter($this->getTagId())
                ->addCustomerFilter(Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerId())
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addAttributeToSelect(Mage::getSingleton('Magento_Catalog_Model_Config')->getProductAttributes())
                ->setActiveFilter()
                ->setVisibility(Mage::getSingleton('Magento_Catalog_Model_Product_Visibility')->getVisibleInSiteIds());
        }
        return $this->_collection;
    }

    /**
     * Product image url getter
     *
     * @param Magento_Catalog_Model_Product $product
     * @return string
     */
    public function getImageUrl($product)
    {
        return (string) $this->_catalogImage->init($product, 'small_image')
            ->resize($this->getImageSize());
    }

    /**
     * Product image size getter
     *
     * @return int
     */
    public function getImageSize()
    {
        return $this->getVar('product_image_size', 'Magento_Tag');
    }

}
