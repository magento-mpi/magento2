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
 * Catalog category helper
 */
class Magento_Catalog_Helper_Product extends Magento_Core_Helper_Url
{
    const XML_PATH_PRODUCT_URL_SUFFIX                = 'catalog/seo/product_url_suffix';
    const XML_PATH_PRODUCT_URL_USE_CATEGORY          = 'catalog/seo/product_use_categories';
    const XML_PATH_USE_PRODUCT_CANONICAL_TAG         = 'catalog/seo/product_canonical_tag';
    const XML_PATH_AUTO_GENERATE_MASK                = 'catalog/fields_masks';
    const XML_PATH_UNASSIGNABLE_ATTRIBUTES           = 'global/catalog/product/attributes/unassignable';
    const XML_PATH_ATTRIBUTES_USED_IN_AUTOGENERATION = 'global/catalog/product/attributes/used_in_autogeneration';
    const XML_PATH_PRODUCT_TYPE_SWITCHER_LABEL       = 'global/catalog/product/attributes/weight/type_switcher/label';

    /**
     * Flag that shows if Magento has to check product to be saleable (enabled and/or inStock)
     *
     * @var boolean
     */
    protected $_skipSaleableCheck = false;

    /**
     * Cache for product rewrite suffix
     *
     * @var array
     */
    protected $_productUrlSuffix = array();

    /**
     * @var array
     */
    protected $_statuses;

    protected $_priceBlock;

    /**
     * @var Magento_Core_Model_View_Url
     */
    protected $_viewUrl;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_View_Url $viewUrl
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_View_Url $viewUrl,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Config $coreConfig
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_eventManager = $eventManager;
        $this->_coreRegistry = $coreRegistry;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_viewUrl = $viewUrl;
        $this->_coreConfig = $coreConfig;
        $this->_logger = $context->getLogger();
        parent::__construct($context);
    }

    /**
     * Retrieve product view page url
     *
     * @param   mixed $product
     * @return  string
     */
    public function getProductUrl($product)
    {
        if ($product instanceof Magento_Catalog_Model_Product) {
            return $product->getProductUrl();
        } elseif (is_numeric($product)) {
            return Mage::getModel('Magento_Catalog_Model_Product')->load($product)->getProductUrl();
        }
        return false;
    }

    /**
     * Retrieve product price
     *
     * @param   Magento_Catalog_Model_Product $product
     * @return  float
     */
    public function getPrice($product)
    {
        return $product->getPrice();
    }

    /**
     * Retrieve product final price
     *
     * @param   Magento_Catalog_Model_Product $product
     * @return  float
     */
    public function getFinalPrice($product)
    {
        return $product->getFinalPrice();
    }

    /**
     * Retrieve base image url
     *
     * @param Magento_Catalog_Model_Product|Magento_Object $product
     * @return string|bool
     */
    public function getImageUrl($product)
    {
        $url = false;
        $attribute = $product->getResource()->getAttribute('image');
        if (!$product->getImage()) {
            $url = $this->_viewUrl->getViewFileUrl('Magento_Catalog::images/product/placeholder/image.jpg');
        } elseif ($attribute) {
            $url = $attribute->getFrontend()->getUrl($product);
        }
        return $url;
    }

    /**
     * Retrieve small image url
     *
     * @param Magento_Catalog_Model_Product|Magento_Object $product
     * @return string|bool
     */
    public function getSmallImageUrl($product)
    {
        $url = false;
        $attribute = $product->getResource()->getAttribute('small_image');
        if (!$product->getSmallImage()) {
            $url = $this->_viewUrl->getViewFileUrl('Magento_Catalog::images/product/placeholder/small_image.jpg');
        } elseif ($attribute) {
            $url = $attribute->getFrontend()->getUrl($product);
        }
        return $url;
    }

    /**
     * Retrieve thumbnail image url
     *
     * @param Magento_Catalog_Model_Product|Magento_Object $product
     * @return string
     */
    public function getThumbnailUrl($product)
    {
        return '';
    }

    /**
     * @param Magento_Catalog_Model_Product $product
     * @return string
     */
    public function getEmailToFriendUrl($product)
    {
        $categoryId = null;
        $category = $this->_coreRegistry->registry('current_category');
        if ($category) {
            $categoryId = $category->getId();
        }
        return $this->_getUrl('sendfriend/product/send', array(
            'id' => $product->getId(),
            'cat_id' => $categoryId
        ));
    }

    /**
     * @return array
     */
    public function getStatuses()
    {
        if (null === $this->_statuses) {
            $this->_statuses = array();
            // Mage::getModel('Magento_Catalog_Model_Product_Status')->getResourceCollection()->load();
        }

        return $this->_statuses;
    }

    /**
     * Check if a product can be shown
     *
     * @param Magento_Catalog_Model_Product|int $product
     * @param string $where
     * @return boolean
     */
    public function canShow($product, $where = 'catalog')
    {
        if (is_int($product)) {
            $product = Mage::getModel('Magento_Catalog_Model_Product')->load($product);
        }

        /* @var $product Magento_Catalog_Model_Product */

        if (!$product->getId()) {
            return false;
        }

        return $product->isVisibleInCatalog() && $product->isVisibleInSiteVisibility();
    }

    /**
     * Retrieve product rewrite sufix for store
     *
     * @param int $storeId
     * @return string
     */
    public function getProductUrlSuffix($storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }

        if (!isset($this->_productUrlSuffix[$storeId])) {
            $this->_productUrlSuffix[$storeId] = $this->_coreStoreConfig->getConfig(
                self::XML_PATH_PRODUCT_URL_SUFFIX, $storeId
            );
        }
        return $this->_productUrlSuffix[$storeId];
    }

    /**
     * Check if <link rel="canonical"> can be used for product
     *
     * @param $store
     * @return bool
     */
    public function canUseCanonicalTag($store = null)
    {
        return $this->_coreStoreConfig->getConfig(self::XML_PATH_USE_PRODUCT_CANONICAL_TAG, $store);
    }

    /**
     * Return information array of product attribute input types
     * Only a small number of settings returned, so we won't break anything in current data flow
     * As soon as development process goes on we need to add there all possible settings
     *
     * @param string $inputType
     * @return array
     */
    public function getAttributeInputTypes($inputType = null)
    {
        /**
         * @todo specify there all relations for properties depending on input type
         */
        $inputTypes = array(
            'multiselect'   => array(
                'backend_model'     => 'Magento_Eav_Model_Entity_Attribute_Backend_Array'
            ),
            'boolean'       => array(
                'source_model'      => 'Magento_Eav_Model_Entity_Attribute_Source_Boolean'
            )
        );

        if (is_null($inputType)) {
            return $inputTypes;
        } else if (isset($inputTypes[$inputType])) {
            return $inputTypes[$inputType];
        }
        return array();
    }

    /**
     * Return default attribute backend model by input type
     *
     * @param string $inputType
     * @return string|null
     */
    public function getAttributeBackendModelByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['backend_model'])) {
            return $inputTypes[$inputType]['backend_model'];
        }
        return null;
    }

    /**
     * Return default attribute source model by input type
     *
     * @param string $inputType
     * @return string|null
     */
    public function getAttributeSourceModelByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['source_model'])) {
            return $inputTypes[$inputType]['source_model'];
        }
        return null;
    }

    /**
     * Inits product to be used for product controller actions and layouts
     * $params can have following data:
     *   'category_id' - id of category to check and append to product as current.
     *     If empty (except FALSE) - will be guessed (e.g. from last visited) to load as current.
     *
     * @param int $productId
     * @param Magento_Core_Controller_Front_Action $controller
     * @param Magento_Object $params
     *
     * @return false|Magento_Catalog_Model_Product
     */
    public function initProduct($productId, $controller, $params = null)
    {
        // Prepare data for routine
        if (!$params) {
            $params = new Magento_Object();
        }

        // Init and load product
        $this->_eventManager->dispatch('catalog_controller_product_init_before', array(
            'controller_action' => $controller,
            'params' => $params,
        ));

        if (!$productId) {
            return false;
        }

        $product = Mage::getModel('Magento_Catalog_Model_Product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($productId);

        if (!$this->canShow($product)) {
            return false;
        }
        if (!in_array(Mage::app()->getStore()->getWebsiteId(), $product->getWebsiteIds())) {
            return false;
        }

        // Load product current category
        $categoryId = $params->getCategoryId();
        if (!$categoryId && ($categoryId !== false)) {
            $lastId = Mage::getSingleton('Magento_Catalog_Model_Session')->getLastVisitedCategoryId();
            if ($product->canBeShowInCategory($lastId)) {
                $categoryId = $lastId;
            }
        } elseif (!$product->canBeShowInCategory($categoryId)) {
            $categoryId = null;
        }

        if ($categoryId) {
            $category = Mage::getModel('Magento_Catalog_Model_Category')->load($categoryId);
            $product->setCategory($category);
            $this->_coreRegistry->register('current_category', $category);
        }

        // Register current data and dispatch final events
        $this->_coreRegistry->register('current_product', $product);
        $this->_coreRegistry->register('product', $product);

        try {
            $this->_eventManager->dispatch('catalog_controller_product_init_after', array(
                'product' => $product,
                'controller_action' => $controller
            ));
        } catch (Magento_Core_Exception $e) {
            $this->_logger->logException($e);
            return false;
        }

        return $product;
    }

    /**
     * Prepares product options by buyRequest: retrieves values and assigns them as default.
     * Also parses and adds product management related values - e.g. qty
     *
     * @param  Magento_Catalog_Model_Product $product
     * @param  Magento_Object $buyRequest
     * @return Magento_Catalog_Helper_Product
     */
    public function prepareProductOptions($product, $buyRequest)
    {
        $optionValues = $product->processBuyRequest($buyRequest);
        $optionValues->setQty($buyRequest->getQty());
        $product->setPreconfiguredValues($optionValues);

        return $this;
    }

    /**
     * Process $buyRequest and sets its options before saving configuration to some product item.
     * This method is used to attach additional parameters to processed buyRequest.
     *
     * $params holds parameters of what operation must be performed:
     * - 'current_config', Magento_Object or array - current buyRequest that configures product in this item,
     *   used to restore currently attached files
     * - 'files_prefix': string[a-z0-9_] - prefix that was added at frontend to names of file inputs,
     *   so they won't intersect with other submitted options
     *
     * @param Magento_Object|array $buyRequest
     * @param Magento_Object|array $params
     * @return Magento_Object
     */
    public function addParamsToBuyRequest($buyRequest, $params)
    {
        if (is_array($buyRequest)) {
            $buyRequest = new Magento_Object($buyRequest);
        }
        if (is_array($params)) {
            $params = new Magento_Object($params);
        }


        // Ensure that currentConfig goes as Magento_Object - for easier work with it later
        $currentConfig = $params->getCurrentConfig();
        if ($currentConfig) {
            if (is_array($currentConfig)) {
                $params->setCurrentConfig(new Magento_Object($currentConfig));
            } else if (!($currentConfig instanceof Magento_Object)) {
                $params->unsCurrentConfig();
            }
        }

        /*
         * Notice that '_processing_params' must always be object to protect processing forged requests
         * where '_processing_params' comes in $buyRequest as array from user input
         */
        $processingParams = $buyRequest->getData('_processing_params');
        if (!$processingParams || !($processingParams instanceof Magento_Object)) {
            $processingParams = new Magento_Object();
            $buyRequest->setData('_processing_params', $processingParams);
        }
        $processingParams->addData($params->getData());

        return $buyRequest;
    }

    /**
     * Return loaded product instance
     *
     * @param  int|string $productId (SKU or ID)
     * @param  int $store
     * @param  string $identifierType
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct($productId, $store, $identifierType = null)
    {
        /** @var $product Magento_Catalog_Model_Product */
        $product = Mage::getModel('Magento_Catalog_Model_Product')->setStoreId(Mage::app()->getStore($store)->getId());

        $expectedIdType = false;
        if ($identifierType === null) {
            if (is_string($productId) && !preg_match("/^[+-]?[1-9][0-9]*$|^0$/", $productId)) {
                $expectedIdType = 'sku';
            }
        }

        if ($identifierType == 'sku' || $expectedIdType == 'sku') {
            $idBySku = $product->getIdBySku($productId);
            if ($idBySku) {
                $productId = $idBySku;
            } else if ($identifierType == 'sku') {
                // Return empty product because it was not found by originally specified SKU identifier
                return $product;
            }
        }

        if ($productId && is_numeric($productId)) {
            $product->load((int) $productId);
        }

        return $product;
    }

    /**
     * Set flag that shows if Magento has to check product to be saleable (enabled and/or inStock)
     *
     * For instance, during order creation in the backend admin has ability to add any products to order
     *
     * @param bool $skipSaleableCheck
     * @return Magento_Catalog_Helper_Product
     */
    public function setSkipSaleableCheck($skipSaleableCheck = false)
    {
        $this->_skipSaleableCheck = $skipSaleableCheck;
        return $this;
    }

    /**
     * Get flag that shows if Magento has to check product to be saleable (enabled and/or inStock)
     *
     * @return boolean
     */
    public function getSkipSaleableCheck()
    {
        return $this->_skipSaleableCheck;
    }

    /**
     * Get masks for auto generation of fields
     *
     * @return array
     */
    public function getFieldsAutogenerationMasks()
    {
        return $this->_coreConfig
            ->getValue(Magento_Catalog_Helper_Product::XML_PATH_AUTO_GENERATE_MASK, 'default');
    }

    /**
     * Retrieve list of attributes that cannot be removed from attribute set
     *
     * @return array
     */
    public function getUnassignableAttributes()
    {
        $data = $this->_coreConfig->getNode(self::XML_PATH_UNASSIGNABLE_ATTRIBUTES);
        return false === $data || is_string($data->asArray()) ? array() : array_keys($data->asArray());
    }

    /**
     * Retrieve list of attributes that allowed for autogeneration
     *
     * @return array
     */
    public function getAttributesAllowedForAutogeneration()
    {
        return array_keys($this->_coreConfig->getNode(self::XML_PATH_ATTRIBUTES_USED_IN_AUTOGENERATION)->asArray());
    }

    /**
     * Get label for virtual control
     *
     * @return string
     */
    public function getTypeSwitcherControlLabel()
    {
        return __((string)$this->_coreConfig->getNode(self::XML_PATH_PRODUCT_TYPE_SWITCHER_LABEL));
    }
}
