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
     * @param int $id
     * @param int $storeId
     * @return \Magento\GiftWrapping\Service\V1\Data\Wrapping
     * @throws InputException
     */
    public function get($id, $storeId = null)
    {
        $wrapping = $this->wrappingRepository->get($id, $storeId);
        return $this->wrappingMapper->extractDto($wrapping);
    }

    /**
     * @param Data\Wrapping $data
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a ID is sent but the entity does not exist
     * @throws \Magento\Framework\Model\Exception If something goes wrong during save
     * @return int
     */
    public function create(Data\Wrapping $data)
    {
        $model = $this->wrappingConverter->getModel($data);

        $imageData = $data->getImage();
        $imageContent = base64_decode($imageData->getBase64Content(), true);
        $model->attachBinaryImage($imageData->getFileName(), $imageContent);

        $model->save();
        return $model->getId();
    }

    /**
     * @param Data\Wrapping $data
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a ID is sent but the entity does not exist
     * @throws \Magento\Framework\Model\Exception If something goes wrong during save
     */
    public function update(Data\Wrapping $data)
    {
        $model = $this->wrappingConverter->loadModel($data);
        $imageData = $data->getImage();
        $imageContent = base64_decode($imageData->getBase64Content(), true);
        $model->attachBinaryImage($imageData->getFileName(), $imageContent);

        $model->save();
    }

    /**
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Framework\Service\V1\Data\SearchResults
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
     * @param int $id
     * @throws \Magento\Framework\Exception\NoSuchEntityException If ID is not found
     * @throws \Exception If something goes wrong during delete
     * @return bool
     */
    public function delete($id)
    {
        $wrapping = $this->wrappingRepository->get($id);
        $wrapping->delete();
        return true;
    }
}
