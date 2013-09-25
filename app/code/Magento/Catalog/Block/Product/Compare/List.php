<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog products compare block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Block_Product_Compare_List extends Magento_Catalog_Block_Product_Compare_Abstract
{
    /**
     * Product Compare items collection
     *
     * @var Magento_Catalog_Model_Resource_Product_Compare_Item_Collection
     */
    protected $_items;

    /**
     * Compare Products comparable attributes cache
     *
     * @var array
     */
    protected $_attributes;

    /**
     * Flag which allow/disallow to use link for as low as price
     *
     * @var bool
     */
    protected $_useLinkForAsLowAs = false;

    /**
     * Customer id
     *
     * @var null|int
     */
    protected $_customerId = null;

    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_noform';

    /**
     * Customer session
     *
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * Log visitor
     *
     * @var Magento_Log_Model_Visitor
     */
    protected $_logVisitor;

    /**
     * Catalog product visibility
     *
     * @var Magento_Catalog_Model_Product_Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * Item collection factory
     *
     * @var Magento_Catalog_Model_Resource_Product_Compare_Item_CollectionFactory
     */
    protected $_itemCollectionFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Catalog_Model_Config $catalogConfig
     * @param Magento_Catalog_Model_Resource_Product_Compare_Item_CollectionFactory $itemCollectionFactory
     * @param Magento_Catalog_Model_Product_Visibility $catalogProductVisibility
     * @param Magento_Log_Model_Visitor $logVisitor
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Catalog_Helper_Product_Compare $catalogProductCompare
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Catalog_Model_Config $catalogConfig,
        Magento_Catalog_Model_Resource_Product_Compare_Item_CollectionFactory $itemCollectionFactory,
        Magento_Catalog_Model_Product_Visibility $catalogProductVisibility,
        Magento_Log_Model_Visitor $logVisitor,
        Magento_Customer_Model_Session $customerSession,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Catalog_Helper_Product_Compare $catalogProductCompare,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_itemCollectionFactory = $itemCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_logVisitor = $logVisitor;
        $this->_customerSession = $customerSession;
        parent::__construct($storeManager, $catalogConfig, $coreRegistry, $catalogProductCompare, $taxData,
            $catalogData, $coreData, $context, $data);
    }

    /**
     * Retrieve url for adding product to wishlist with params
     *
     * @param Magento_Catalog_Model_Product $product
     * @return string
     */
    public function getAddToWishlistUrl($product)
    {
        $continueUrl    = $this->_coreData->urlEncode($this->getUrl('customer/account'));
        $urlParamName   = Magento_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;

        $params = array(
            $urlParamName   => $continueUrl
        );

        return $this->helper('Magento_Wishlist_Helper_Data')->getAddUrlWithParams($product, $params);
    }

    /**
     * Preparing layout
     *
     * @return Magento_Catalog_Block_Product_Compare_List
     */
    protected function _prepareLayout()
    {
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Products Comparison List') . ' - ' . $headBlock->getDefaultTitle());
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrieve Product Compare items collection
     *
     * @return Magento_Catalog_Model_Resource_Product_Compare_Item_Collection
     */
    public function getItems()
    {
        if (is_null($this->_items)) {
            $this->_catalogProductCompare->setAllowUsedFlat(false);

            $this->_items = $this->_itemCollectionFactory->create();
            $this->_items->useProductItem(true)
                ->setStoreId($this->_storeManager->getStore()->getId());

            if ($this->_customerSession->isLoggedIn()) {
                $this->_items->setCustomerId($this->_customerSession->getCustomerId());
            } elseif ($this->_customerId) {
                $this->_items->setCustomerId($this->_customerId);
            } else {
                $this->_items->setVisitorId($this->_logVisitor->getId());
            }

            $this->_items
                ->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
                ->loadComparableAttributes()
                ->addMinimalPrice()
                ->addTaxPercents()
                ->setVisibility($this->_catalogProductVisibility->getVisibleInSiteIds());
        }

        return $this->_items;
    }

    /**
     * Retrieve Product Compare Attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            $this->_attributes = $this->getItems()->getComparableAttributes();
        }

        return $this->_attributes;
    }

    /**
     * Retrieve Product Attribute Value
     *
     * @param Magento_Catalog_Model_Product $product
     * @param Magento_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return string
     */
    public function getProductAttributeValue($product, $attribute)
    {
        if (!$product->hasData($attribute->getAttributeCode())) {
            return __('N/A');
        }

        if ($attribute->getSourceModel()
            || in_array($attribute->getFrontendInput(), array('select','boolean','multiselect'))
        ) {
            //$value = $attribute->getSource()->getOptionText($product->getData($attribute->getAttributeCode()));
            $value = $attribute->getFrontend()->getValue($product);
        } else {
            $value = $product->getData($attribute->getAttributeCode());
        }
        return ((string)$value == '') ? __('No') : $value;
    }

    /**
     * Retrieve Print URL
     *
     * @return string
     */
    public function getPrintUrl()
    {
        return $this->getUrl('*/*/*', array('_current'=>true, 'print'=>1));
    }

    /**
     * Setter for customer id
     *
     * @param int $id
     * @return Magento_Catalog_Block_Product_Compare_List
     */
    public function setCustomerId($id)
    {
        $this->_customerId = $id;
        return $this;
    }
}
