<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Service\V1\Data;

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
     * Create model based on data object. Load model if data object has ID specified.
     *
     * @param Wrapping $dataObject
     * @param int|null $modelId Model ID to load
     * @return \Magento\GiftWrapping\Model\Wrapping
     */
    public function getModel(Wrapping $dataObject, $modelId = null)
    {
        $model = $this->wrappingFactory->create();
        if ($modelId) {
            $model->load($modelId);
        }
        $model->addData($dataObject->__toArray());
        $imageContent = base64_decode($dataObject->getImageBase64Content(), true);
        $model->attachBinaryImage($dataObject->getImageName(), $imageContent);
        return $model;
    }
}
