<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\Attribute;

use \Magento\Framework\Exception\InputException;
use \Magento\Framework\Exception\NoSuchEntityException;

class Repository implements \Magento\Catalog\Api\ProductAttributeRepositoryInterface
{
    /**
     * @var \Magento\Catalog\Model\Resource\Attribute
     */
    protected $attributeResource;

    /**
     * @var \Magento\Eav\Model\AttributeRepository
     */
    protected $eavAttributeRepository;

    /**
     * @var \Magento\Catalog\Api\Data\ProductAttributeInterfaceDataBuilder
     */
    protected $attributeBuilder;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory
     */
    protected $inputtypeValidatorFactory;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $productHelper;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filterManager;

    /**
     * @var \Magento\Framework\Service\Config\MetadataConfig
     */
    protected $metadataConfig;

    /**
     * @var \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Service\V1\Data\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @param \Magento\Catalog\Model\Resource\Attribute $attributeResource
     * @param \Magento\Catalog\Api\Data\ProductAttributeInterfaceDataBuilder $attributeBuilder
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     * @param \Magento\Eav\Model\AttributeRepository $eavAttributeRepository
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory $validatorFactory
     * @param \Magento\Framework\Service\Config\MetadataConfig $metadataConfig
     * @param \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Service\V1\Data\FilterBuilder $filterBuilder
     */
    public function __construct(
        \Magento\Catalog\Model\Resource\Attribute $attributeResource,
        \Magento\Catalog\Api\Data\ProductAttributeInterfaceDataBuilder $attributeBuilder,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Eav\Model\AttributeRepository $eavAttributeRepository,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory $validatorFactory,
        \Magento\Framework\Service\Config\MetadataConfig $metadataConfig,
        \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Service\V1\Data\FilterBuilder $filterBuilder
    ) {
        $this->attributeResource = $attributeResource;
        $this->attributeBuilder = $attributeBuilder;
        $this->productHelper = $productHelper;
        $this->filterManager = $filterManager;
        $this->eavAttributeRepository = $eavAttributeRepository;
        $this->eavConfig = $eavConfig;
        $this->inputtypeValidatorFactory = $validatorFactory;
        $this->metadataConfig = $metadataConfig;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function get($attributeCode)
    {
        return $this->eavAttributeRepository->get(
            \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE,
            $attributeCode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria)
    {
        return $this->eavAttributeRepository->getList(
            \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE,
            $searchCriteria
        );
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Catalog\Api\Data\ProductAttributeInterface $attribute)
    {
        $attributeData = $this->attributeBuilder->populateWithArray($attribute->getData());

        if ($attribute->getAttributeId()) {
            $existingModel = $this->get($attribute->getAttributeCode());

            if (!$existingModel->getAttributeId()) {
                throw NoSuchEntityException::singleField('attribute_code', $existingModel->getAttributeCode());
            }

            $attributeData->setAttributeId($existingModel->getAttributeId());
            $attributeData->setIsUserDefined($existingModel->getIsUserDefined());
            $attributeData->setFrontendInput($existingModel->getFrontendInput());

            if ($attribute->getStoreFrontendLabels() && is_array($attribute->getStoreFrontendLabels())) {
                $frontendLabel[0] = $existingModel->getFrontendLabel();
                foreach ($attribute->getStoreFrontendLabels() as $item) {
                    $frontendLabel[$item->getStoreId()] = $item->getLabel();
                }
                $attributeData->setFrontendLabel($frontendLabel);
            }
            if (!$attribute->getIsUserDefined()) {
                // Unset attribute field for system attributes
                $attributeData->setApplyTo(null);
            }
        } else {
            $attributeData->setAttributeId(null);

            if (!$attribute->getStoreFrontendLabels()) {
                throw InputException::requiredField('frontend_label');
            }
            $frontendLabels = [];
            foreach ($attribute->getStoreFrontendLabels() as $label) {
                $frontendLabels[$label->getStoreId()] = $label->getLabel();
            }
            if (!isset($frontendLabels[0]) || !$frontendLabels[0]) {
                throw InputException::invalidFieldValue('frontend_label', null);
            }

            $attributeData->setFrontendLabel($frontendLabels);
            $attributeData->setAttributeCode(
                $attribute->getAttributeCode() ?: $this->generateCode($frontendLabels[0])
            );
            $this->validateCode($attribute->getAttributeCode());
            $this->validateFrontendInput($attribute->getFrontendInput());

            $attributeData->setBackendType(
                $attribute->getBackendTypeByInput($attribute->getFrontendInput())
            );
            $attributeData->setSourceModel(
                $this->productHelper->getAttributeSourceModelByInputType($attribute->getFrontendInput())
            );
            $attributeData->setBackendModel(
                $this->productHelper->getAttributeBackendModelByInputType($attribute->getFrontendInput())
            );
            $attributeData->setEntityTypeId($this->eavConfig
                ->getEntityType(\Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE)->getId()
            );
            $attributeData->setIsUserDefined(1);
        }
        $attribute = $attributeData->create();
        $this->attributeResource->save($attribute);
        return $attribute;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Magento\Catalog\Api\Data\ProductAttributeInterface $attribute)
    {
        $model = $this->eavConfig->getAttribute(
            \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE,
            $attribute->getAttributeId()
        );

        if (!$model || !$model->getId()) {
            throw NoSuchEntityException::singleField('attribute_code', $attribute->getAttributeId());
        }

        $this->attributeResource->delete($attribute);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($attributeCode)
    {
        $this->delete(
            $this->get($attributeCode)
        );
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomAttributesMetadata($dataObjectClassName = null)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(
            [
                $this->filterBuilder
                    ->setField('attribute_set_id')
                    ->setValue(\Magento\Catalog\Api\Data\ProductAttributeInterface::DEFAULT_ATTRIBUTE_SET_ID)
                    ->create()
            ]
        );

        $customAttributes = [];
        $entityAttributes = $this->getList($searchCriteria->create())->getItems();

        foreach ($entityAttributes as $attributeMetadata) {
            $customAttributes[] = $attributeMetadata;
        }
        return array_merge($customAttributes, $this->metadataConfig->getCustomAttributesMetadata($dataObjectClassName));
    }

    /**
     * Generate code from label
     *
     * @param string $label
     * @return string
     */
    protected function generateCode($label)
    {
        $code = substr(preg_replace('/[^a-z_0-9]/', '_', $this->filterManager->translitUrl($label)), 0, 30);
        $validatorAttrCode = new \Zend_Validate_Regex(['pattern' => '/^[a-z][a-z_0-9]{0,29}[a-z0-9]$/']);
        if (!$validatorAttrCode->isValid($code)) {
            $code = 'attr_' . ($code ?: substr(md5(time()), 0, 8));
        }
        return $code;
    }

    /**
     * Validate attribute code
     *
     * @param string $code
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function validateCode($code)
    {
        $validatorAttrCode = new \Zend_Validate_Regex(['pattern' => '/^[a-z][a-z_0-9]{0,30}$/']);
        if (!$validatorAttrCode->isValid($code)) {
            throw InputException::invalidFieldValue('attribute_code', $code);
        }
    }

    /**
     * Validate Frontend Input Type
     *
     * @param  string $frontendInput
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function validateFrontendInput($frontendInput)
    {
        /** @var \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\Validator $validator */
        $validator = $this->inputtypeValidatorFactory->create();
        if (!$validator->isValid($frontendInput)) {
            throw InputException::invalidFieldValue('frontend_input', $frontendInput);
        }
    }
}
