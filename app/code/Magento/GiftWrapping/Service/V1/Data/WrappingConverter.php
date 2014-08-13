<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Service\V1\Data;

use \Magento\Framework\Exception\NoSuchEntityException;

class WrappingConverter
{
    /**
     * @var \Magento\GiftWrapping\Model\WrappingFactory
     */
    protected $wrappingFactory;

    /**
     * @param \Magento\GiftWrapping\Model\WrappingFactory $wrappingFactory
     */
    public function __construct(\Magento\GiftWrapping\Model\WrappingFactory $wrappingFactory)
    {
        $this->wrappingFactory = $wrappingFactory;
    }

    /**
     * Create new model based on data object
     *
     * @param \Magento\Framework\Service\Data\AbstractObject $dataObject
     * @return \Magento\GiftWrapping\Model\Wrapping
     */
    public function getModel(\Magento\Framework\Service\Data\AbstractObject $dataObject)
    {
        return $this->wrappingFactory->create()->setData($dataObject->__toArray());
    }

    /**
     * Load model and assign data object
     *
     * @param Wrapping $data
     * @return \Magento\GiftWrapping\Model\Wrapping
     * @throws NoSuchEntityException
     */
    public function loadModel(Wrapping $data)
    {
        /** @var \Magento\GiftWrapping\Model\Wrapping $wrapping */
        $wrapping = $this->wrappingFactory->create();
        $wrapping->load($data->getWrappingId());
        if (!$wrapping->getId()) {
            throw new NoSuchEntityException(
                'Gift Wrapping with specified ID "%1" not found',
                [$data->getWrappingId()]
            );
        }
        $wrapping->addData($data->__toArray());
        return $wrapping;
    }
}
