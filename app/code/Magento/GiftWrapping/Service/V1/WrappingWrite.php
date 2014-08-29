<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Service\V1;

use Magento\GiftWrapping\Model\WrappingRepository;
use Magento\GiftWrapping\Service\V1\Data\WrappingConverter;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;

class WrappingWrite implements WrappingWriteInterface
{
    /**
     * @var WrappingRepository
     */
    protected $wrappingRepository;

    /**
     * @var WrappingConverter
     */
    protected $wrappingConverter;

    /**
     * @param WrappingRepository $wrappingRepository
     * @param WrappingConverter $wrappingConverter
     */
    public function __construct(
        WrappingRepository $wrappingRepository,
        WrappingConverter $wrappingConverter
    ) {
        $this->wrappingRepository = $wrappingRepository;
        $this->wrappingConverter = $wrappingConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function create(Data\Wrapping $data)
    {
        if ($data->getWrappingId()) {
            throw new InputException('Parameter id is not expected for this request.');
        }
        $model = $this->wrappingConverter->getModel($data);
        $model->save();
        return $model->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, Data\Wrapping $data)
    {
        if ($data->getWrappingId()) {
            throw new InputException('Parameter id is not expected for this request.');
        }
        $model = $this->wrappingConverter->getModel($data, $id);
        if (!$model->getId()) {
            throw new NoSuchEntityException('Gift Wrapping with ID "%1" not found.', [$id]);
        }
        $model->save();
        return $model->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $model = $this->wrappingRepository->get($id);
        $model->delete();
        return true;
    }
}
