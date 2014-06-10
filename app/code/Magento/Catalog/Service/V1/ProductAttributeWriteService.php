<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata;
use Magento\Framework\Service\EavDataObjectConverter;
use Magento\Framework\Exception\InputException;

/**
 * Class ProductAttributeWriteService
 * @package Magento\Catalog\Service\V1
 */
class ProductAttributeWriteService implements ProductAttributeWriteServiceInterface
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Catalog\Model\Resource\Eav\AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filter;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $helper;

    /**
     * @var \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\Validator
     */
    protected $inputValidator;

    /**
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\Resource\Eav\AttributeFactory $attributeFactory
     * @param \Magento\Framework\Filter\FilterManager $filter
     * @param \Magento\Catalog\Helper\Product $helper
     * @param \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\Validator $inputValidator
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Resource\Eav\AttributeFactory $attributeFactory,
        \Magento\Framework\Filter\FilterManager $filter,
        \Magento\Catalog\Helper\Product $helper,
        \Magento\Eav\Model\Adminhtml\System\Config\Source\Inputtype\Validator $inputValidator
    ) {
        $this->eavConfig = $eavConfig;
        $this->attributeFactory = $attributeFactory;
        $this->filter = $filter;
        $this->helper = $helper;
        $this->inputValidator = $inputValidator;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($attributeId)
    {
        $model = $this->eavConfig->getAttribute(ProductMetadataServiceInterface::ENTITY_TYPE_PRODUCT, $attributeId);
        if (!$model) {
            //product attribute does not exist
            throw NoSuchEntityException::singleField(AttributeMetadata::ATTRIBUTE_ID, $attributeId);
        }
        $model->delete();
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function create(\Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata $attributeMetadata)
    {
        if (!$attributeMetadata->getFrontendLabel()) {
            throw InputException::invalidFieldValue('frontend_label', $attributeMetadata->getFrontendLabel());
        }

        /**
         * @var $model \Magento\Catalog\Model\Resource\Eav\Attribute
         */
        $model = $this->attributeFactory->create();
        $data = EavDataObjectConverter::toFlatArray($attributeMetadata);
        $data['attribute_code'] =
            $attributeMetadata->getAttributeCode() ?: $this->generateCode($data['frontend_label']);
        $this->validateCode($data['attribute_code']);
        $this->validateFrontendInput($attributeMetadata->getFrontendInput());

        $data['backend_type'] = $model->getBackendTypeByInput($attributeMetadata->getFrontendInput());
        $data['source_model'] =
            $this->helper->getAttributeSourceModelByInputType($attributeMetadata->getFrontendInput());
        $data['backend_model'] =
            $this->helper->getAttributeBackendModelByInputType($attributeMetadata->getFrontendInput());

        $model->addData($data);

        $model->setEntityTypeId($this->eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)->getId())
            ->setIsUserDefined(1);

        return $model->save()->getId();
    }

    /**
     * Generate code from label
     *
     * @param string $label
     * @return string
     */
    protected function generateCode($label)
    {
        $code = substr(preg_replace('/[^a-z_0-9]/', '_', $this->filter->translitUrl($label)), 0, 30);
        $validatorAttrCode = new \Zend_Validate_Regex(array('pattern' => '/^[a-z][a-z_0-9]{0,29}[a-z0-9]$/'));
        if (!$validatorAttrCode->isValid($code)) {
            $code = 'attr_' . ($code ?: substr(md5(time()), 0, 8));
        }
        return $code;
    }

    /**
     * Validate attribute code
     *
     * @param $code
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
     * @param $frontendInput
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function validateFrontendInput($frontendInput)
    {
        if (!$this->inputValidator->isValid($frontendInput)) {
            throw InputException::invalidFieldValue('frontend_input', $frontendInput);
        }
    }
}
