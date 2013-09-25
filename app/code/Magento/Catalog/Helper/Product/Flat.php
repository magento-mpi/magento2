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
 * Catalog Product Flat Helper
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Helper_Product_Flat extends Magento_Catalog_Helper_Flat_Abstract
{
    /**
     * Catalog Product Flat Config
     */
    const XML_PATH_USE_PRODUCT_FLAT          = 'catalog/frontend/flat_catalog_product';

    /**
     * @var int
     */
    protected $_addFilterableAttrs;

    /**
     * @var int
     */
    protected $_addChildData;
    
    /**
     * Catalog Flat Product index process code
     */
    const CATALOG_FLAT_PROCESS_CODE = 'catalog_product_flat';

    /**
     * Catalog Product Flat index process code
     *
     * @var string
     */
    protected $_indexerCode = self::CATALOG_FLAT_PROCESS_CODE;

    /**
     * Catalog Product Flat index process instance
     *
     * @var Magento_Index_Model_Process|null
     */
    protected $_process = null;

    /**
     * Store flags which defines if Catalog Product Flat functionality is enabled
     *
     * @deprecated after 1.7.0.0
     *
     * @var array
     */
    protected $_isEnabled = array();

    /**
     * Catalog Product Flat Flag object
     *
     * @var Magento_Catalog_Model_Product_Flat_Flag
     */
    protected $_flagObject;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * Construct
     *
     * @param Magento_Index_Model_ProcessFactory $processFactory

     * 
     * Constructor
     * @param Magento_Index_Model_ProcessFactory $processFactory
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Catalog_Model_Product_Flat_Flag $flatFlag
     * @param $addFilterableAttrs
     * @param $addChildData
     */
    public function __construct(
        Magento_Index_Model_ProcessFactory $processFactory,
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Catalog_Model_Product_Flat_Flag $flatFlag,
        $addFilterableAttrs,
        $addChildData
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($processFactory, $context);
        $this->_flagObject = $flatFlag;
        $this->_addFilterableAttrs = intval($addFilterableAttrs);
        $this->_addChildData = intval($addChildData);
    }

    /**
     * Retrieve Catalog Product Flat Flag object
     *
     * @return Magento_Catalog_Model_Product_Flat_Flag
     */
    public function getFlag()
    {
        if (is_null($this->_flagObject)) {
            $this->_flagObject->loadSelf();
        }
        return $this->_flagObject;
    }

    /**
     * Check Catalog Product Flat functionality is enabled
     *
     * @param int|string|null|Magento_Core_Model_Store $store this parameter is deprecated and no longer in use
     *
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return $this->_coreStoreConfig->getConfigFlag(self::XML_PATH_USE_PRODUCT_FLAT);
    }

    /**
     * Check if Catalog Product Flat Data has been initialized
     *
     * @return bool
     */
    public function isBuilt()
    {
        return $this->getFlag()->getIsBuilt();
    }

    /**
     * Is add filterable attributes to Flat table
     *
     * @return int
     */
    public function isAddFilterableAttributes()
    {
        return $this->_addFilterableAttrs;
    }

    /**
     * Is add child data to Flat
     *
     * @return int
     */
    public function isAddChildData()
    {
        return $this->_addChildData;
    }
}
