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
use Magento\GiftWrapping\Service\V1\Data\WrappingConverter;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;

class Wrapping implements WrappingInterface
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
     * @var WrappingConverter
     */
    protected $wrappingConverter;

    /**
     * @var Data\WrappingSearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @param WrappingRepository $wrappingRepository
     * @param WrappingMapper $wrappingMapper
     * @param WrappingConverter $wrappingConverter
     * @param Data\WrappingSearchResultsBuilder $searchResultsBuilder
     */
    public function __construct(
        WrappingRepository $wrappingRepository,
        WrappingMapper $wrappingMapper,
        WrappingConverter $wrappingConverter,
        Data\WrappingSearchResultsBuilder $searchResultsBuilder
    ) {
        $this->wrappingRepository = $wrappingRepository;
        $this->wrappingMapper = $wrappingMapper;
        $this->wrappingConverter = $wrappingConverter;
        $this->searchResultsBuilder = $searchResultsBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id, $storeId = null)
    {
        $wrapping = $this->wrappingRepository->get($id, $storeId);
        return $this->wrappingMapper->extractDto($wrapping);
    }

    /**
     * {@inheritdoc}
     */
    public function create(Data\Wrapping $data)
    {
        if ($data->getWrappingId()) {
            throw new InputException('Parameter Wrapping ID not supported in create operation');
        }
        $model = $this->wrappingConverter->getModel($data);
        $model->save();
        return $model->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function update(Data\Wrapping $data)
    {
        $model = $this->wrappingConverter->getModel($data);
        if (!$model->getId()) {
            throw new NoSuchEntityException('Gift Wrapping with ID "%1" not found', [$data->getWrappingId()]);
        }
        $model->save();
        return $model->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function search(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria)
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

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $wrapping = $this->wrappingRepository->get($id);
        $wrapping->delete();
        return true;
    }
}
