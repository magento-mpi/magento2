<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

class ProductAttributeSetReadService implements ProductAttributeSetReadServiceInterface
{
    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    protected $setFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\Set\CollectionFactory
     */
    protected $setCollectionFactory;

    /**
     * @var Data\Eav\AttributeSetBuilder
     */
    protected $attributeSetBuilder;

    /**
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Set\CollectionFactory $setCollectionFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param Data\Eav\AttributeSetBuilder $attributeSetBuilder
     */
    public function __construct(
        \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory,
        \Magento\Eav\Model\Resource\Entity\Attribute\Set\CollectionFactory $setCollectionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Service\V1\Data\Eav\AttributeSetBuilder $attributeSetBuilder
    ) {
        $this->setFactory = $setFactory;
        $this->setCollectionFactory = $setCollectionFactory;
        $this->productFactory = $productFactory;
        $this->attributeSetBuilder = $attributeSetBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        $sets = array();

        $attributeSetsCollection = $this->setCollectionFactory->create()
            ->setEntityTypeFilter($this->productFactory->create()->getResource()->getTypeId())
            ->load();

        /** @var $attributeSet \Magento\Eav\Model\Resource\Entity\Attribute\Set */
        foreach($attributeSetsCollection as $attributeSet) {
            $this->attributeSetBuilder->setId($attributeSet->getId());
            $this->attributeSetBuilder->setName($attributeSet->getAttributeSetName());
            $this->attributeSetBuilder->setSortOrder($attributeSet->getSortOrder());
            $sets[] = $this->attributeSetBuilder->create();
        }

        return $sets;
    }
}
