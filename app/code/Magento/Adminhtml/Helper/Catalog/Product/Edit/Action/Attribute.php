<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml catalog product action attribute update helper
 */
class Magento_Adminhtml_Helper_Catalog_Product_Edit_Action_Attribute extends Magento_Backend_Helper_Data
{
    /**
     * Selected products for mass-update
     *
     * @var Magento_Catalog_Model_Entity_Product_Collection
     */
    protected $_products;

    /**
     * Array of same attributes for selected products
     *
     * @var Magento_Eav_Model_Resource_Entity_Attribute_Collection
     */
    protected $_attributes;

    /**
     * Excluded from batch update attribute codes
     *
     * @var array
     */
    protected $_excludedAttributes = array('url_key');

    /**
     * @var Magento_Catalog_Model_Resource_Product_CollectionFactory
     */
    protected $_productsFactory;

    /**
     * @var Magento_Backend_Model_Session
     */
    protected $_session;

    /**
     * @var Magento_Eav_Model_Config
     */
    protected $_eavConfig;

    /**
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Backend_Model_Session $session
     * @param Magento_Catalog_Model_Resource_Product_CollectionFactory $productsFactory
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_ConfigInterface $applicationConfig
     * @param Magento_Core_Model_Config_Primary $primaryConfig
     * @param Magento_Core_Model_RouterList $routerList
     * @param Magento_Core_Model_AppProxy $app
     * @param Magento_Backend_Model_UrlProxy $backendUrl
     * @param Magento_Backend_Model_AuthProxy $auth
     * @param $defaultAreaFrontName
     */
    public function __construct(
        Magento_Eav_Model_Config $eavConfig,
        Magento_Backend_Model_Session $session,
        Magento_Catalog_Model_Resource_Product_CollectionFactory $productsFactory,
        Magento_Core_Helper_Context $context,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Model_ConfigInterface $applicationConfig,
        Magento_Core_Model_Config_Primary $primaryConfig,
        Magento_Core_Model_RouterList $routerList,
        Magento_Core_Model_AppProxy $app,
        Magento_Backend_Model_UrlProxy $backendUrl,
        Magento_Backend_Model_AuthProxy $auth,
        $defaultAreaFrontName
    ) {
        $this->_eavConfig = $eavConfig;
        $this->_session = $session;
        $this->_productsFactory = $productsFactory;
        parent::__construct($context, $coreData, $applicationConfig, $primaryConfig, $routerList, $app, $backendUrl,
            $auth, $defaultAreaFrontName);
    }

    /**
     * Return product collection with selected product filter
     * Product collection didn't load
     *
     * @return Magento_Catalog_Model_Resource_Product_Collection
     */
    public function getProducts()
    {
        if (is_null($this->_products)) {
            $productsIds = $this->getProductIds();

            if (!is_array($productsIds)) {
                $productsIds = array(0);
            }

            $this->_products = $this->_productsFactory->create()
                ->setStoreId($this->getSelectedStoreId())
                ->addIdFilter($productsIds);
        }

        return $this->_products;
    }

    /**
     * Return array of selected product ids from post or session
     *
     * @return array|null
     */
    public function getProductIds()
    {
        if ($this->_getRequest()->isPost() && $this->_getRequest()->getActionName() == 'edit') {
            $this->_session->setProductIds($this->_getRequest()->getParam('product', null));
        }

        return $this->_session->getProductIds();
    }

    /**
     * Return selected store id from request
     *
     * @return integer
     */
    public function getSelectedStoreId()
    {
        return (int)$this->_getRequest()->getParam('store', Magento_Core_Model_AppInterface::ADMIN_STORE_ID);
    }

    /**
     * Return array of attribute sets by selected products
     *
     * @return array
     */
    public function getProductsSetIds()
    {
        return $this->getProducts()->getSetIds();
    }

    /**
     * Return collection of same attributes for selected products without unique
     *
     * @return Magento_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            $this->_attributes  = $this->_eavConfig->getEntityType(Magento_Catalog_Model_Product::ENTITY)
                ->getAttributeCollection()
                ->addIsNotUniqueFilter()
                ->setInAllAttributeSetsFilter($this->getProductsSetIds());

            if ($this->_excludedAttributes) {
                $this->_attributes->addFieldToFilter('attribute_code', array('nin' => $this->_excludedAttributes));
            }

            // check product type apply to limitation and remove attributes that impossible to change in mass-update
            $productTypeIds  = $this->getProducts()->getProductTypeIds();
            foreach ($this->_attributes as $attribute) {
                /* @var $attribute Magento_Catalog_Model_Entity_Attribute */
                foreach ($productTypeIds as $productTypeId) {
                    $applyTo = $attribute->getApplyTo();
                    if (count($applyTo) > 0 && !in_array($productTypeId, $applyTo)) {
                        $this->_attributes->removeItemByKey($attribute->getId());
                        break;
                    }
                }
            }
        }

        return $this->_attributes;
    }
}
