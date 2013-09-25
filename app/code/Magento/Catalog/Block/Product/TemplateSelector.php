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
 * Product gallery
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_Catalog_Block_Product_TemplateSelector extends Magento_Core_Block_Template
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * Set collection factory
     *
     * @var Magento_Eav_Model_Resource_Entity_Attribute_Set_CollectionFactory
     */
    protected $_setCollectionFactory;

    /**
     * Catalog resource helper
     *
     * @var Magento_Catalog_Model_Resource_Helper
     */
    protected $_resourceHelper;

    /**
     * Construct
     *
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Set_CollectionFactory $setCollectionFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Catalog_Model_Resource_Helper $resourceHelper
     * @param array $data
     */
    public function __construct(
        Magento_Eav_Model_Resource_Entity_Attribute_Set_CollectionFactory $setCollectionFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Catalog_Model_Resource_Helper $resourceHelper,
        array $data = array()
    ) {
        $this->_setCollectionFactory = $setCollectionFactory;
        $this->_coreRegistry = $registry;
        $this->_resourceHelper = $resourceHelper;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve list of product templates with search part contained in label
     *
     * @param string $labelPart
     * @return array
     */
    public function getSuggestedTemplates($labelPart)
    {
        $product = $this->_coreRegistry->registry('product');
        $entityType = $product->getResource()->getEntityType();
        $labelPart = $this->_resourceHelper->addLikeEscape($labelPart, array('position' => 'any'));
        /** @var Magento_Eav_Model_Resource_Entity_Attribute_Set_Collection $collection */
        $collection = $this->_setCollectionFactory->create();
        $collection->setEntityTypeFilter($entityType->getId())
            ->addFieldToFilter('attribute_set_name', array('like' => $labelPart))
            ->addFieldToSelect('attribute_set_id', 'id')
            ->addFieldToSelect('attribute_set_name', 'label')
            ->setOrder(
                'attribute_set_name',
                Magento_Eav_Model_Resource_Entity_Attribute_Set_Collection::SORT_ORDER_ASC
        );
        return $collection->getData();
    }
}
