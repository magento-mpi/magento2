<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Api;

use Magento\Framework\Api\AbstractExtensibleObjectBuilder;
use Magento\Framework\Api\AttributeValueBuilder;
use Magento\Framework\Api\MetadataServiceInterface;
use Magento\Framework\Api\ObjectFactory;

/**
 * Builder for the SearchResults Service Data Object
 *
 * @method SearchResults create()
 */
abstract class AbstractSearchResultsBuilder extends AbstractExtensibleObjectBuilder
{
    /**
     * Search criteria builder
     *
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Item data object builder
     *
     * @var AbstractExtensibleObjectBuilder $itemObjectBuilder
     */
    protected $itemObjectBuilder;

    /**
     * Constructor
     *
     * @param ObjectFactory $objectFactory
     * @param AttributeValueBuilder $valueBuilder
     * @param MetadataServiceInterface $metadataService
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param AbstractExtensibleObjectBuilder $itemObjectBuilder
     */
    public function __construct(
        ObjectFactory $objectFactory,
        AttributeValueBuilder $valueBuilder,
        MetadataServiceInterface $metadataService,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        AbstractExtensibleObjectBuilder $itemObjectBuilder
    ) {
        parent::__construct($objectFactory, $valueBuilder, $metadataService);
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->itemObjectBuilder = $itemObjectBuilder;
    }

    /**
     * Set search criteria
     *
     * @param SearchCriteria $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(SearchCriteria $searchCriteria)
    {
        return $this->_set(SearchResults::KEY_SEARCH_CRITERIA, $searchCriteria);
    }

    /**
     * Set total count
     *
     * @param int $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount)
    {
        return $this->_set(SearchResults::KEY_TOTAL_COUNT, $totalCount);
    }

    /**
     * Set items
     *
     * @param \Magento\Framework\Api\AbstractExtensibleObject[] $items
     * @return $this
     */
    public function setItems($items)
    {
        return $this->_set(SearchResults::KEY_ITEMS, $items);
    }

    /**
     * {@inheritdoc}
     */
    protected function _setDataValues(array $data)
    {
        if (array_key_exists(SearchResults::KEY_SEARCH_CRITERIA, $data)) {
            $data[SearchResults::KEY_SEARCH_CRITERIA] =
                $this->searchCriteriaBuilder->populateWithArray($data[SearchResults::KEY_SEARCH_CRITERIA])->create();
        }
        if (array_key_exists(SearchResults::KEY_ITEMS, $data)) {
            $items = [];
            foreach ($data[SearchResults::KEY_ITEMS] as $itemArray) {
                $items[] = $this->itemObjectBuilder->populateWithArray($itemArray)->create();
            }
            $data[SearchResults::KEY_ITEMS] = $items;
        }
        return parent::_setDataValues($data);
    }
}
