<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute;

use Magento\Framework\Service\EavDataObjectConverter;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata;
use Magento\Catalog\Service\V1\Data\Eav\Product\Attribute\FrontendLabel;

/**
 * Class WriteService
 * @package Magento\Catalog\Service\V1\Product\Attribute
 */
class WriteService implements WriteServiceInterface
{
    /**
     * @var \Magento\Catalog\Model\Resource\Eav\AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory
     */
    protected $inputtypeValidatorFactory;

    /**
     * @param \Magento\Catalog\Model\Resource\Eav\AttributeFactory $attributeFactory
     * @param \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory $inputtypeValidatorFactory
     */
    public function __construct(
        \Magento\Catalog\Model\Resource\Eav\AttributeFactory $attributeFactory,
        \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory $inputtypeValidatorFactory
    )
    {
        $this->attributeFactory = $attributeFactory;
        $this->inputtypeValidatorFactory = $inputtypeValidatorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, AttributeMetadata $attribute)
    {
        /** @var \Magento\Catalog\Model\Resource\Eav\Attribute $attributeModel */
        $model = $this->attributeFactory->create();
        $model->loadByCode(\Magento\Catalog\Model\Product::ENTITY, $id);
        if (!$model->getId()) {
            throw NoSuchEntityException::singleField(AttributeMetadata::ATTRIBUTE_CODE, $id);
        }

        $data = $attribute->__toArray();

        // this fields should not be changed
        $data[AttributeMetadata::ATTRIBUTE_ID]   = $model->getAttributeId();
        $data[AttributeMetadata::USER_DEFINED]   = $model->getIsUserDefined();
        $data[AttributeMetadata::FRONTEND_INPUT] = $model->getFrontendInput();

        if (isset($data[AttributeMetadata::FRONTEND_LABEL]) && is_array($data[AttributeMetadata::FRONTEND_LABEL])) {
            $frontendLabel[0] = $model->getFrontendLabel();
            foreach ($data[AttributeMetadata::FRONTEND_LABEL] as $item) {
                if (isset($item[FrontendLabel::STORE_ID], $item[FrontendLabel::LABEL])) {
                    $frontendLabel[$item[FrontendLabel::STORE_ID]] = $item[FrontendLabel::LABEL];
                }
            }
            $data[AttributeMetadata::FRONTEND_LABEL] = $frontendLabel;
        }

        if (!$model->getIsUserDefined()) {
            // Unset attribute field for system attributes
            unset($data[AttributeMetadata::APPLY_TO]);
        }

        try {
            $model->addData($data);
            $model->save();
        } catch(\Exception $e) {
            throw new CouldNotSaveException('Could not update product attribute' . $e->getMessage());
        }

        return $model->getAttributeCode();
    }
}
