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
     * @return \Magento\GiftWrapping\Model\Wrapping
     */
    public function getModel(Wrapping $dataObject)
    {
        $model = $this->wrappingFactory->create();
        if ($dataObject->getWrappingId()) {
            $model->load($dataObject->getWrappingId());
        }
        $model->addData($dataObject->__toArray());

        $imageDataObject = $dataObject->getImage();
        $imageContent = base64_decode($imageDataObject->getBase64Content(), true);
        $model->attachBinaryImage($imageDataObject->getFileName(), $imageContent);

        return $model;
    }
}
