<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Service\V1;

use Magento\GiftWrapping\Model\WrappingRepository;
use Magento\GiftWrapping\Service\V1\Data\WrappingMapper;

class WrappingRead implements WrappingReadInterface
{
    /**
     * @var WrappingRepository
     */
    protected $wrappingRepository;

    /**
     * @var WrappingMapper
     */
    protected $wrappingMapper;

    /**
     * @var Data\WrappingSearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @param WrappingRepository $wrappingRepository
     * @param WrappingMapper $wrappingMapper
     * @param Data\WrappingSearchResultsBuilder $searchResultsBuilder
     */
    public function __construct(
        WrappingRepository $wrappingRepository,
        WrappingMapper $wrappingMapper,
        Data\WrappingSearchResultsBuilder $searchResultsBuilder
    ) {
        $this->wrappingRepository = $wrappingRepository;
        $this->wrappingMapper = $wrappingMapper;
        $this->searchResultsBuilder = $searchResultsBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $wrapping = $this->wrappingRepository->get($id);
        return $this->wrappingMapper->extractDto($wrapping);
    }

    /**
     * {@inheritdoc}
     */
    public function search(\Magento\Framework\Data\SearchCriteria $searchCriteria)
    {
        $wrappings = [];
        foreach ($this->wrappingRepository->find($searchCriteria) as $wrapping) {
            $wrappings[] = $this->wrappingMapper->extractDto($wrapping);
        }
        return $this->searchResultsBuilder->setItems($wrappings)
            ->setTotalCount(count($wrappings))
            ->setSearchCriteria($searchCriteria)
            ->create();
    }
}
