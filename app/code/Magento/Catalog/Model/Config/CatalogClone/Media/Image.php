<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Config\CatalogClone\Media;

/**
 * Clone model for media images related config fields
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Image extends \Magento\Core\Model\Config\Value
{
    /**
     * Eav config
     *
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * Attribute collection factory
     *
     * @var \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory
     */
    protected $_attributeCollectionFactory;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\ConfigInterface $config,
        \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
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
        $entityTypeId = $this->_eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)->getId();

        /* @var $collection \Magento\Catalog\Model\Resource\Product\Attribute\Collection */
        $collection = $this->_attributeCollectionFactory->create();
        $collection->setEntityTypeFilter($entityTypeId);
        $collection->setFrontendInputTypeFilter('media_image');

        $prefixes = array();

        foreach ($collection as $attribute) {
            /* @var $attribute \Magento\Eav\Model\Entity\Attribute */
            $prefixes[] = array(
                'field' => $attribute->getAttributeCode() . '_',
                'label' => $attribute->getFrontend()->getLabel()
            );
        }

        return $prefixes;
    }
}
