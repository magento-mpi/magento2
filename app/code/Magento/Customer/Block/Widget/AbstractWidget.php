<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Widget;

class AbstractWidget extends \Magento\View\Element\Template
{
    /**
     * @var \Magento\Customer\Service\V1\CustomerMetadataServiceInterface
     */
    protected $_attributeMetadata;

    /**
     * @var \Magento\Customer\Helper\Address
     */
    protected $_addressHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Address $addressHelper
     * @param \Magento\Customer\Service\V1\CustomerMetadataServiceInterface $attributeMetadata
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Address $addressHelper,
        \Magento\Customer\Service\V1\CustomerMetadataServiceInterface $attributeMetadata,
        array $data = array()
    ) {
        $this->_addressHelper = $addressHelper;
        $this->_attributeMetadata = $attributeMetadata;
        parent::__construct($context, $data);
    }

    public function getConfig($key)
    {
        return $this->_addressHelper->getConfig($key);
    }

    public function getFieldIdFormat()
    {
        if (!$this->hasData('field_id_format')) {
            $this->setData('field_id_format', '%s');
        }
        return $this->getData('field_id_format');
    }

    public function getFieldNameFormat()
    {
        if (!$this->hasData('field_name_format')) {
            $this->setData('field_name_format', '%s');
        }
        return $this->getData('field_name_format');
    }

    public function getFieldId($field)
    {
        return sprintf($this->getFieldIdFormat(), $field);
    }

    public function getFieldName($field)
    {
        return sprintf($this->getFieldNameFormat(), $field);
    }

    /**
     * Retrieve customer attribute instance
     *
     * @param string $attributeCode
     * @return \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata
     */
    protected function _getAttribute($attributeCode)
    {
        return $this->_attributeMetadata->getAttributeMetadata('customer', $attributeCode);
    }
}
