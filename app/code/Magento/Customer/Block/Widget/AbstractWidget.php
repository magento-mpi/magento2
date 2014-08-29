<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Widget;

class AbstractWidget extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Service\V1\CustomerMetadataServiceInterface
     */
    protected $customerMetadataService;

    /**
     * @var \Magento\Customer\Helper\Address
     */
    protected $_addressHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Address $addressHelper
     * @param \Magento\Customer\Service\V1\CustomerMetadataServiceInterface $customerMetadataService
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Address $addressHelper,
        \Magento\Customer\Service\V1\CustomerMetadataServiceInterface $customerMetadataService,
        array $data = array()
    ) {
        $this->_addressHelper = $addressHelper;
        $this->customerMetadataService = $customerMetadataService;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * @param string $key
     * @return null|string
     */
    public function getConfig($key)
    {
        return $this->_addressHelper->getConfig($key);
    }

    /**
     * @return string
     */
    public function getFieldIdFormat()
    {
        if (!$this->hasData('field_id_format')) {
            $this->setData('field_id_format', '%s');
        }
        return $this->getData('field_id_format');
    }

    /**
     * @return string
     */
    public function getFieldNameFormat()
    {
        if (!$this->hasData('field_name_format')) {
            $this->setData('field_name_format', '%s');
        }
        return $this->getData('field_name_format');
    }

    /**
     * @param string $field
     * @return string
     */
    public function getFieldId($field)
    {
        return sprintf($this->getFieldIdFormat(), $field);
    }

    /**
     * @param string $field
     * @return string
     */
    public function getFieldName($field)
    {
        return sprintf($this->getFieldNameFormat(), $field);
    }

    /**
     * Retrieve customer attribute instance
     *
     * @param string $attributeCode
     * @return \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata|null
     */
    protected function _getAttribute($attributeCode)
    {
        try {
            return $this->customerMetadataService->getAttributeMetadata($attributeCode);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }
    }
}
