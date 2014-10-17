<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Attribute;

use \Magento\Framework\Exception\InputException;
use \Magento\Framework\Exception\NoSuchEntityException;

class Repository implements \Magento\Catalog\Api\ProductAttributeRepositoryInterface
{
    /**
     * @var \Magento\Catalog\Model\Resource\Eav\Attribute
     */
    protected $attributeResource;

    /**
     * @var \Magento\Framework\Data\Search\SearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @var \Magento\Eav\Model\AttributeRepository
     */
    protected $eavAttributeRepository;

    /**
     * @var \Magento\Framework\Data\Search\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Data\Search\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\IdentifierFactory
     */
    protected $attributeIdentifierFactory;

    /**
     * @var \Magento\Catalog\Model\Entity\AttributeBuilder
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
     * @param \Magento\Catalog\Model\Resource\Attribute $attributeResource
     * @param \Magento\Catalog\Model\Entity\AttributeBuilder $attributeBuilder
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Framework\Data\Search\SearchResultsBuilder $searchResultsBuilder
     * @param \Magento\Framework\Data\Search\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Data\Search\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     * @param \Magento\Eav\Model\AttributeRepository $eavAttributeRepository
     * @param \Magento\Eav\Model\Entity\Attribute\IdentifierFactory $attributeIdentifierFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory $validatorFactory
     */
    public function __construct(
        \Magento\Catalog\Model\Resource\Attribute $attributeResource,
        \Magento\Catalog\Model\Entity\AttributeBuilder $attributeBuilder,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Framework\Data\Search\SearchResultsBuilder $searchResultsBuilder,
        \Magento\Framework\Data\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Data\Search\FilterBuilder $filterBuilder,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Eav\Model\AttributeRepository $eavAttributeRepository,
        \Magento\Eav\Model\Entity\Attribute\IdentifierFactory $attributeIdentifierFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorFactory $validatorFactory
    ) {
        $this->attributeResource = $attributeResource;
        $this->attributeBuilder = $attributeBuilder;
        $this->productHelper = $productHelper;
        $this->searchResultsBuilder = $searchResultsBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterManager = $filterManager;
        $this->eavAttributeRepository = $eavAttributeRepository;
        $this->attributeIdentifierFactory = $attributeIdentifierFactory;
        $this->eavConfig = $eavConfig;
        $this->inputtypeValidatorFactory = $validatorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function get($attributeCode, array $arguments = [])
    {
        $identifier = $this->attributeIdentifierFactory->create([
            'attributeCode' => $attributeCode,
            'entityTypeCode' => \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE
        ]);
        return $this->eavAttributeRepository->get($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Data\Search\SearchCriteriaInterface $searchCriteria,
        array $arguments = []
    ) {
        $this->searchCriteriaBuilder->setFilterGroups($searchCriteria->getFilterGroups());
        $this->searchCriteriaBuilder->setSortOrders($searchCriteria->getSortOrders());
        $this->searchCriteriaBuilder->setPageSize($searchCriteria->getPageSize());
        $this->searchCriteriaBuilder->setCurrentPage($searchCriteria->getCurrentPage());

        $this->searchCriteriaBuilder->addFilter(
            [
                $this->filterBuilder
                    ->setField('entityTypeCode')
                    ->setValue(\Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE)
                    ->create()
            ]
        );

        return $this->eavAttributeRepository->getList($this->searchCriteriaBuilder->create());
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Catalog\Api\Data\ProductAttributeInterface $attribute, array $arguments = [])
    {
        $attributeData = $this->attributeBuilder->populate($attribute);

        if ($attribute->getAttributeId()) {
            $existingModel = $this->get($attribute->getAttributeCode());

            if (!$existingModel->getAttributeId()) {
                throw NoSuchEntityException::singleField('attribute_code', $existingModel->getAttributeCode());
            }

            $attributeData->setAttributeId($existingModel->getAttributeId());
            $attributeData->setIsUserDefined($existingModel->isUserDefined());
            $attributeData->setFrontendInput($existingModel->getFrontendInput());

            if ($attribute->getFrontendLabel() && is_array($attribute->getFrontendLabel())) {
                $frontendLabel[0] = $existingModel->getFrontendLabel();
                foreach ($attribute->getFrontendLabel() as $item) {
                    if (isset($item['store_id'], $item['label'])) {
                        $frontendLabel[$item['store_id']] = $item['label'];
                    }
                }
                $attributeData->setFrontendLabel($frontendLabel);
            }

            if (!$attribute->isUserDefined()) {
                // Unset attribute field for system attributes
                $attributeData->setApplyTo(null);
            }

        } else {
            $attributeData->setAttributeId(null);

            if (!$attribute->getFrontendLabel()) {
                throw InputException::requiredField('frontend_label');
            }
            $frontendLabels = [];
            foreach ($attribute->getFrontendLabel() as $label) {
                $frontendLabels[$label->getStoreId()] = $label->getLabel();
            }
            if (!isset($frontendLabels[0]) || !$frontendLabels[0]) {
                throw InputException::invalidFieldValue('frontend_label', null);
            }

            $attributeData->setFronendLabel($frontendLabels);
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

        $this->attributeResource->save($attributeData->create());
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Magento\Catalog\Api\Data\ProductAttributeInterface $attribute, array $arguments = [])
    {
        $this->attributeResource->delete($attribute);
        return true;
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
        $validatorAttrCode = new \Zend_Validate_Regex(array('pattern' => '/^[a-z][a-z_0-9]{0,29}[a-z0-9]$/'));
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
        $validatorAttrCode = new \Zend_Validate_Regex(array('pattern' => '/^[a-z][a-z_0-9]{0,30}$/'));
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
