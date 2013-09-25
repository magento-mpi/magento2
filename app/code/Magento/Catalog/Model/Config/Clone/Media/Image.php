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
 * Clone model for media images related config fields
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_Catalog_Model_Config_Clone_Media_Image extends Magento_Core_Model_Config_Value
{
    /**
     * Eav config
     *
     * @var Magento_Eav_Model_Config
     */
    protected $_eavConfig;

    /**
     * Attribute collection factory
     *
     * @var Magento_Catalog_Model_Resource_Product_Attribute_CollectionFactory
     */
    protected $_attributeCollectionFactory;

    /**
     * Construct
     *
     * @param Magento_Catalog_Model_Resource_Product_Attribute_CollectionFactory $attributeCollectionFactory
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Model_Resource_Product_Attribute_CollectionFactory $attributeCollectionFactory,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_attributeCollectionFactory = $attributeCollectionFactory;
        $this->_eavConfig = $eavConfig;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Get fields prefixes
     *
     * @return array
     */
    public function getPrefixes()
    {
        // use cached eav config
        $entityTypeId = $this->_eavConfig->getEntityType(Magento_Catalog_Model_Product::ENTITY)->getId();

        /* @var $collection Magento_Catalog_Model_Resource_Product_Attribute_Collection */
        $collection = $this->_attributeCollectionFactory->create();
        $collection->setEntityTypeFilter($entityTypeId);
        $collection->setFrontendInputTypeFilter('media_image');

        $prefixes = array();

        foreach ($collection as $attribute) {
            /* @var $attribute Magento_Eav_Model_Entity_Attribute */
            $prefixes[] = array(
                'field' => $attribute->getAttributeCode() . '_',
                'label' => $attribute->getFrontend()->getLabel(),
            );
        }

        return $prefixes;
    }

}
