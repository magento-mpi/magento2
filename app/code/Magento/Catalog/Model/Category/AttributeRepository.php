<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Category;

use Magento\Catalog\Api\CategoryAttributeRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Catalog\Api\Data\CategoryAttributeInterface;

class AttributeRepository implements CategoryAttributeRepositoryInterface
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->eavConfig = $eavConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Data\Search\SearchCriteriaInterface $searchCriteria)
    {
        $categoryEntityType = CategoryAttributeInterface::ENTITY_TYPE_CODE;


        $this->searchResultsBuilder->setSearchCriteria($searchCriteria);
        /** @var \Magento\Eav\Model\Resource\Entity\Attribute\Collection $attributeCollection */
        $attributeCollection = $this->attributeCollectionFactory->create();
        $attributeCollection->join(
            array('entity_type' => $attributeCollection->getTable('eav_entity_type')),
            'main_table.entity_type_id = entity_type.entity_type_id',
            []
        );
        $attributeCollection->addFieldToFilter('entity_type_code', ['eq' => $categoryEntityType]);
        $attributeCollection->join(
            ['eav_entity_attribute' => $attributeCollection->getTable('eav_entity_attribute')],
            'main_table.attribute_id = eav_entity_attribute.attribute_id',
            []
        );
        $attributeCollection->join(
            array('additional_table' => $attributeCollection->getTable('catalog_eav_attribute')),
            'main_table.attribute_id = additional_table.attribute_id',
            []
        );
        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $attributeCollection);
        }
        /** @var SortOrder $sortOrder */
        foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
            $attributeCollection->addOrder(
                $this->translateField($sortOrder->getField()),
                ($sortOrder->getDirection() == SearchCriteria::SORT_ASC) ? 'ASC' : 'DESC'
            );
        }

        $totalCount = $attributeCollection->getSize();

        // Group attributes by id to prevent duplicates with different attribute sets
        $attributeCollection->addAttributeGrouping();

        $attributeCollection->setCurPage($searchCriteria->getCurrentPage());
        $attributeCollection->setPageSize($searchCriteria->getPageSize());

        $attributes = [];
        /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
        foreach ($attributeCollection as $attribute) {
            $attributes[] = $this->getAttributeMetadata($categoryEntityType, $attribute->getAttributeCode());
        }
        $this->searchResultsBuilder->setItems($attributes);
        $this->searchResultsBuilder->setTotalCount($totalCount);
        return $this->searchResultsBuilder->create();
    }

    /**
     * {@inheritdoc}
     */
    public function get($attributeCode)
    {
        $categoryEntityType = CategoryAttributeInterface::ENTITY_TYPE_CODE;
        /** @var AbstractAttribute $attribute */
        $attribute = $this->eavConfig->getAttribute($categoryEntityType, $attributeCode);
        if (!$attribute) {
            throw (new NoSuchEntityException('entityType', array($categoryEntityType)))
                ->singleField('attributeCode', $attributeCode);
        }
        return $attribute;
    }
}
