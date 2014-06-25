<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Model\Resource\Entity\Attribute\Collection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata;
use Magento\Catalog\Service\V1\Data\Eav\Product\Attribute\FrontendLabel;
use Magento\Framework\Service\V1\Data\Search\FilterGroup;
use Magento\Framework\Service\V1\Data\SearchCriteria;

/**
 * Class MetadataService
 * @package Magento\Catalog\Service\V1
 */
class MetadataService implements MetadataServiceInterface
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @var \Magento\Framework\App\ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * @var Data\Eav\AttributeMetadataBuilder
     */
    private $attributeMetadataBuilder;

    /** @var  \Magento\Eav\Model\Resource\Entity\Attribute\CollectionFactory */
    private $attributeCollectionFactory;
    /**
     * @var Data\Product\SearchResultsBuilder
     */
    private $searchResultsBuilder;
    /**
     * @var Data\Eav\AttributeMetadataBuilder
     */
    private $attributeBuilder;

    /**
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\ScopeResolverInterface $scopeResolver
     * @param Data\Eav\AttributeMetadataBuilder $attributeMetadataBuilder
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\CollectionFactory $attributeCollectionFactory
     * @param Data\Product\SearchResultsBuilder $searchResultsBuilder
     * @param Data\Eav\AttributeMetadataBuilder $attributeBuilder
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ScopeResolverInterface $scopeResolver,
        Data\Eav\AttributeMetadataBuilder $attributeMetadataBuilder,
        \Magento\Eav\Model\Resource\Entity\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Catalog\Service\V1\Data\Product\SearchResultsBuilder $searchResultsBuilder,
        \Magento\Catalog\Service\V1\Data\Eav\AttributeMetadataBuilder $attributeBuilder
    ) {
        $this->eavConfig = $eavConfig;
        $this->scopeResolver = $scopeResolver;
        $this->attributeMetadataBuilder = $attributeMetadataBuilder;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->searchResultsBuilder = $searchResultsBuilder;
        $this->attributeBuilder = $attributeBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeMetadata($entityType, $attributeCode)
    {
        /** @var AbstractAttribute $attribute */
        $attribute = $this->eavConfig->getAttribute($entityType, $attributeCode);
        if ($attribute) {
            $attributeMetadata = $this->createMetadataAttribute($attribute);
            return $attributeMetadata;
        } else {
            throw (new NoSuchEntityException('entityType', array($entityType)))
                ->singleField('attributeCode', $attributeCode);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAllAttributeMetadata(
        $entityType,
        SearchCriteria $searchCriteria
    ) {
        $this->searchResultsBuilder->setSearchCriteria($searchCriteria);
        /** @var \Magento\Eav\Model\Resource\Entity\Attribute\Collection $attributeCollection */
        $attributeCollection = $this->attributeCollectionFactory->create();

        $attributeCollection->join(
            array('entity_type' => $attributeCollection->getTable('eav_entity_type')),
            'main_table.entity_type_id = entity_type.entity_type_id',
            []
        );
        $attributeCollection->addFieldToFilter('entity_type_code', ['eq' => $entityType]);
        $attributeCollection->join(
            ['eav_entity_attribute' => $attributeCollection->getTable('eav_entity_attribute')],
            'main_table.attribute_id = eav_entity_attribute.attribute_id',
            ['attribute_set_id']
        );

        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $attributeCollection);
        }
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $field => $direction) {
                $attributeCollection->addOrder($field, $direction == SearchCriteria::SORT_ASC ? 'ASC' : 'DESC');
            }
        }

        $attributeCollection->join(
            array('additional_table' => $attributeCollection->getTable('catalog_eav_attribute')),
            'main_table.attribute_id = additional_table.attribute_id',
            [
                'frontend_input_renderer',
                'is_global',
                'is_visible',
                'is_searchable',
                'is_filterable',
                'is_comparable',
                'is_visible_on_front',
                'is_html_allowed_on_front',
                'is_used_for_price_rules',
                'is_filterable_in_search',
                'used_in_product_listing',
                'used_for_sort_by',
                'apply_to',
                'is_visible_in_advanced_search',
                'position',
                'is_wysiwyg_enabled',
                'is_used_for_promo_rules',
                'is_configurable',
                'search_weight',
            ]
        );

        $attributeCollection->setCurPage($searchCriteria->getCurrentPage());
        $attributeCollection->setPageSize($searchCriteria->getPageSize());
        $this->searchResultsBuilder->setTotalCount($attributeCollection->getSize());

        $attributes = [];
        /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
        foreach ($attributeCollection as $attribute) {
            $attributes[] = $this->attributeBuilder
               ->setAttributeId($attribute->getAttributeId())
               ->setAttributeCode($attribute->getAttributeCode())
               ->setFrontendLabel($attribute->getData('frontend_label'))
               ->setDefaultValue($attribute->getDefaultValue())
               ->setRequired((boolean)$attribute->getData('is_required'))
               ->setUserDefined((boolean)$attribute->getData('is_user_defined'))
               ->setFrontendInput($attribute->getData('frontend_input'))
               ->create();
        }

        $this->searchResultsBuilder->setItems($attributes);
        return $this->searchResultsBuilder->create();
    }

    /**
     * @param  AbstractAttribute $attribute
     * @return Data\Eav\AttributeMetadata
     */
    private function createMetadataAttribute($attribute)
    {
        $data = $this->booleanPrefixMapper($attribute->getData());

        // fill options and validate rules
        $data[AttributeMetadata::OPTIONS] = $attribute->usesSource()
            ? $attribute->getSource()->getAllOptions() : array();
        $data[AttributeMetadata::VALIDATION_RULES] = $attribute->getValidateRules();

        // fill scope
        $data[AttributeMetadata::SCOPE] = $attribute->isScopeGlobal()
            ? 'global' : ($attribute->isScopeWebsite() ? 'website' : 'store');

        $data[AttributeMetadata::FRONTEND_LABEL] = [];
        $data[AttributeMetadata::FRONTEND_LABEL][0] = array(
            FrontendLabel::STORE_ID => 0,
            FrontendLabel::LABEL => $attribute->getFrontendLabel()
        );
        if (is_array($attribute->getStoreLabels())) {
            foreach ($attribute->getStoreLabels() as $storeId => $label) {
                $data[AttributeMetadata::FRONTEND_LABEL][$storeId] = array(
                    FrontendLabel::STORE_ID => $storeId,
                    FrontendLabel::LABEL => $label
                );
            }
        }
        return $this->attributeMetadataBuilder->populateWithArray($data)->create();
    }

    /**
     * Remove 'is_' prefixes for Attribute fields to make DTO interface more natural
     *
     * @param array $attributeFields
     * @return array
     */
    private function booleanPrefixMapper(array $attributeFields)
    {
        $prefix = 'is_';
        foreach ($attributeFields as $key => $value) {
            if (strpos($key, $prefix) !== 0) {
                continue;
            }
            $postfix = substr($key, strlen($prefix));
            if (!isset($attributeFields[$postfix])) {
                $attributeFields[$postfix] = $value;
                unset($attributeFields[$key]);
            }
        }
        return $attributeFields;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Service\V1\Data\Search\FilterGroup  $filterGroup
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Collection $collection
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $collection)
    {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }
}
