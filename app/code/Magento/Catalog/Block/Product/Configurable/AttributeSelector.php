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
 * Select attributes suitable for product variations generation
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_Catalog_Block_Product_Configurable_AttributeSelector extends Magento_Backend_Block_Template
{
    /**
     * Attribute collection factory
     *
     * @var Magento_Catalog_Model_Resource_Product_Attribute_CollectionFactory
     */
    protected $_attributeCollectionFactory;

    /**
     * Catalog resource helper
     *
     * @var Magento_Catalog_Model_Resource_Helper
     */
    protected $_resourceHelper;

    /**
     * Construct
     *
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Catalog_Model_Resource_Product_Attribute_CollectionFactory $attributeCollectionFactory
     * @param Magento_Catalog_Model_Resource_Helper $resourceHelper
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Catalog_Model_Resource_Product_Attribute_CollectionFactory $attributeCollectionFactory,
        Magento_Catalog_Model_Resource_Helper $resourceHelper,
        array $data = array()
    ) {
        $this->_attributeCollectionFactory = $attributeCollectionFactory;
        $this->_resourceHelper = $resourceHelper;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve list of attributes with admin store label containing $labelPart
     *
     * @param string $labelPart
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function getSuggestedAttributes($labelPart)
    {
        $escapedLabelPart = $this->_resourceHelper->addLikeEscape($labelPart, array('position' => 'any'));
        /** @var $collection Magento_Catalog_Model_Resource_Product_Attribute_Collection */
        $collection = $this->_attributeCollectionFactory->create();
        $collection->addFieldToFilter('frontend_input', 'select')
            ->addFieldToFilter('frontend_label', array('like' => $escapedLabelPart))
            ->addFieldToFilter('is_configurable', 1)
            ->addFieldToFilter('is_user_defined', 1)
            ->addFieldToFilter('is_global', Magento_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL);

        $result = array();
        $types = array(
            Magento_Catalog_Model_Product_Type::TYPE_SIMPLE,
            Magento_Catalog_Model_Product_Type::TYPE_VIRTUAL,
            Magento_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
        );
        foreach ($collection->getItems() as $id => $attribute) {
            /** @var $attribute Magento_Catalog_Model_Resource_Eav_Attribute */
            if (!$attribute->getApplyTo() || count(array_diff($types, $attribute->getApplyTo())) === 0) {
                $result[$id] = array(
                    'id'      => $attribute->getId(),
                    'label'   => $attribute->getFrontendLabel(),
                    'code'    => $attribute->getAttributeCode(),
                    'options' => $attribute->getSource()->getAllOptions(false)
                );
            }
        }
        return $result;
    }

    /**
     * Attribute set creation action URL
     *
     * @return string
     */
    public function getAttributeSetCreationUrl()
    {
        return $this->getUrl('*/catalog_product_set/save');
    }

    /**
     * Get options for suggest widget
     *
     * @return array
     */
    public function getSuggestWidgetOptions()
    {
        return array(
            'source' => $this->getUrl('*/catalog_product_attribute/suggestConfigurableAttributes'),
            'minLength' => 0,
            'className' => 'category-select',
            'showAll' => true,
        );
    }
}
