<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\Catalog\Model\Product\Attribute\Source\InputtypeFactory;
use Magento\Catalog\Service\V1\Data\Eav\Attribute;
use Magento\Framework\Service\V1\Data\Search\FilterGroup;
use Magento\Eav\Model\Resource\Entity\Attribute\Collection;

/**
 * Class ProductAttributeReadService
 * @package Magento\Catalog\Service\V1
 */
class ProductAttributeReadService implements ProductAttributeReadServiceInterface
{
    /**
     * @var ProductMetadataServiceInterface
     */
    private $metadataService;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\InputtypeFactory
     */
    private $inputTypeFactory;

    /**
     * @var Data\ProductAttributeTypeBuilder
     */
    private $attributeTypeBuilder;

    /**
     * @var \Magento\Catalog\Service\V1\Data\Product\Attribute\SearchResultsBuilder
     */
    private $searchResultsBuilder;

    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\Collection
     */
    private $attributeCollection;

    /**
     * @var \Magento\Catalog\Service\V1\Data\Eav\AttributeBuilder
     */
    protected $attributeBuilder;

    /**
     * @param ProductMetadataServiceInterface $metadataService
     * @param InputtypeFactory $inputTypeFactory
     * @param Data\ProductAttributeTypeBuilder $attributeTypeBuilder
     * @param \Magento\Catalog\Service\V1\Data\Product\Attribute\SearchResultsBuilder $searchResultsBuilder
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Collection $attributeCollection
     * @param \Magento\Catalog\Service\V1\Data\Eav\AttributeBuilder $attributeBuilder
     */
    public function __construct(
        ProductMetadataServiceInterface $metadataService,
        InputtypeFactory $inputTypeFactory,
        Data\ProductAttributeTypeBuilder $attributeTypeBuilder,
        \Magento\Catalog\Service\V1\Data\Product\Attribute\SearchResultsBuilder $searchResultsBuilder,
        \Magento\Eav\Model\Resource\Entity\Attribute\Collection $attributeCollection,
        \Magento\Catalog\Service\V1\Data\Eav\AttributeBuilder $attributeBuilder
    ) {
        $this->metadataService = $metadataService;
        $this->inputTypeFactory = $inputTypeFactory;
        $this->attributeTypeBuilder = $attributeTypeBuilder;
        $this->searchResultsBuilder = $searchResultsBuilder;
        $this->attributeCollection = $attributeCollection;
        $this->attributeBuilder = $attributeBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function types()
    {
        $types = [];
        $inputType = $this->inputTypeFactory->create();

        foreach ($inputType->toOptionArray() as $option) {
            $types[] = $this->attributeTypeBuilder->populateWithArray($option)->create();
        }
        return $types;
    }

    /**
     * {@inheritdoc}
     */
    public function info($id)
    {
        return $this->metadataService->getAttributeMetadata(
            ProductMetadataServiceInterface::ENTITY_TYPE_PRODUCT,
            $id
        );
    }

    /**
     * {@inheritdoc}
     */
    public function search(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria)
    {
        $this->searchResultsBuilder->setSearchCriteria($searchCriteria);

        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $this->attributeCollection);
        }
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $field => $direction) {
                $field = $this->translateField($field);
                $this->attributeCollection->addOrder($field, $direction == SearchCriteria::SORT_ASC ? 'ASC' : 'DESC');
            }
        }

        $this->attributeCollection->join(
            array('additional_table' => $this->attributeCollection->getTable('catalog_eav_attribute')),
            'main_table.attribute_id = additional_table.attribute_id'
        );

        $this->attributeCollection->setCurPage($searchCriteria->getCurrentPage());
        $this->attributeCollection->setPageSize($searchCriteria->getPageSize());
        $this->searchResultsBuilder->setTotalCount($this->attributeCollection->getSize());

        $attributes = array();
        /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
        foreach ($this->attributeCollection as $attribute) {
            $attributes[] = $this->attributeBuilder->setId($attribute->getAttributeId())
                ->setCode($attribute->getAttributeCode())
                ->setFrontendLabel($attribute->getData('frontend_label'))
                ->setDefaultValue($attribute->getDefaultValue())
                ->setIsRequired((boolean)$attribute->getData('is_required'))
                ->setIsUserDefined((boolean)$attribute->getData('is_user_defined'))
                ->setFrontendInput($attribute->getData('frontend_input'))
                ->create();
        }

        $this->searchResultsBuilder->setItems($attributes);
        return $this->searchResultsBuilder->create();
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Collection $collection
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $collection)
    {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = $this->translateField($filter->getField());
            $conditions[] = [$condition => $filter->getValue()];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * Translates a field name to a DB column name for use in collection queries.
     *
     * @param string $field a field name that should be translated to a DB column name.
     * @return string
     */
    protected function translateField($field)
    {
        switch ($field) {
            case Attribute::ID:
                return 'attribute_id';
            case Attribute::CODE:
                return 'attribute_code';
            default:
                return $field;
        }
    }
}
