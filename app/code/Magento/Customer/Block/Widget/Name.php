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

use Magento\Customer\Service\V1\CustomerMetadataServiceInterface;
use Magento\Customer\Service\V1\Dto\Customer;
use Magento\View\Element\Template\Context;
use Magento\Customer\Helper\Address as AddressHelper;
use Magento\Customer\Helper\Data as CustomerHelper;

/**
 * Widget for showing customer name.
 *
 * @method \Magento\Customer\Service\V1\Dto\Customer getObject()
 * @method Name setObject(\Magento\Customer\Service\V1\Dto\Customer $customer)
 */
class Name extends AbstractWidget
{
    /**
     * @var CustomerHelper
     */
    protected $_customerHelper;

    /**
     * @param Context $context
     * @param AddressHelper $addressHelper
     * @param CustomerMetadataServiceInterface $attributeMetadata,
     * @param CustomerHelper $customerHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        AddressHelper $addressHelper,
        CustomerMetadataServiceInterface $attributeMetadata,
        CustomerHelper $customerHelper,
        array $data = array()
    ) {
        $this->_customerHelper = $customerHelper;
        parent::__construct($context, $addressHelper, $attributeMetadata, $data);
        $this->_isScopePrivate = true;
    }

    public function _construct()
    {
        parent::_construct();

        // default template location
        $this->setTemplate('widget/name.phtml');
    }

    /**
     * Can show config value
     *
     * @param string $key
     * @return bool
     */
    protected function _showConfig($key)
    {
        return (bool)$this->getConfig($key);
    }

    /**
     * Can show prefix
     *
     * @return bool
     */
    public function showPrefix()
    {
        return (bool)$this->_getAttribute('prefix')->isVisible();
    }

    /**
     * Define if prefix attribute is required
     *
     * @return bool
     */
    public function isPrefixRequired()
    {
        return (bool)$this->_getAttribute('prefix')->isRequired();
    }

    /**
     * Retrieve name prefix drop-down options
     *
     * @return array|bool
     */
    public function getPrefixOptions()
    {
        $prefixOptions = $this->_customerHelper->getNamePrefixOptions();

        if ($this->getObject() && !empty($prefixOptions)) {
            $oldPrefix = $this->escapeHtml(trim($this->getObject()->getPrefix()));
            $prefixOptions[$oldPrefix] = $oldPrefix;
        }
        return $prefixOptions;
    }

    /**
     * Define if middle name attribute can be shown
     *
     * @return bool
     */
    public function showMiddlename()
    {
        return (bool)$this->_getAttribute('middlename')->isVisible();
    }

    /**
     * Define if middlename attribute is required
     *
     * @return bool
     */
    public function isMiddlenameRequired()
    {
        return (bool)$this->_getAttribute('middlename')->isRequired();
    }

    /**
     * Define if suffix attribute can be shown
     *
     * @return bool
     */
    public function showSuffix()
    {
        return (bool)$this->_getAttribute('suffix')->isVisible();
    }

    /**
     * Define if suffix attribute is required
     *
     * @return bool
     */
    public function isSuffixRequired()
    {
        return (bool)$this->_getAttribute('suffix')->isRequired();
    }

    /**
     * Retrieve name suffix drop-down options
     *
     * @return array|bool
     */
    public function getSuffixOptions()
    {
        $suffixOptions = $this->_customerHelper->getNameSuffixOptions();
        if ($this->getObject() && !empty($suffixOptions)) {
            $oldSuffix = $this->escapeHtml(trim($this->getObject()->getSuffix()));
            $suffixOptions[$oldSuffix] = $oldSuffix;
        }
        return $suffixOptions;
    }

    /**
     * Class name getter
     *
     * @return string
     */
    public function getClassName()
    {
        if (!$this->hasData('class_name')) {
            $this->setData('class_name', 'customer-name');
        }
        return $this->getData('class_name');
    }

    /**
     * Container class name getter
     *
     * @return string
     */
    public function getContainerClassName()
    {
        $class = $this->getClassName();
        $class .= $this->showPrefix() ? '-prefix' : '';
        $class .= $this->showMiddlename() ? '-middlename' : '';
        $class .= $this->showSuffix() ? '-suffix' : '';
        return $class;
    }

    /**
     * Retrieve customer or customer address attribute instance
     *
     * @param string $attributeCode
     * @return \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata
     */
    protected function _getAttribute($attributeCode)
    {
        if ($this->getForceUseCustomerAttributes()
            || $this->getObject() instanceof \Magento\Customer\Model\Customer
            || $this->getObject() instanceof Customer) {
            return parent::_getAttribute($attributeCode);
        }

        $attribute = $this->_attributeMetadata->getAddressAttributeMetadata($attributeCode);

        if ($this->getForceUseCustomerRequiredAttributes() && $attribute && !$attribute->isRequired()) {
            $customerAttribute = parent::_getAttribute($attributeCode);
            if ($customerAttribute && $customerAttribute->isRequired()) {
                $attribute = $customerAttribute;
            }
        }

        return $attribute;
    }

    /**
     * Retrieve store attribute label
     *
     * @param string $attributeCode
     * @return string
     */
    public function getStoreLabel($attributeCode)
    {
        $attribute = $this->_getAttribute($attributeCode);
        return $attribute ? __($attribute->getStoreLabel()) : '';
    }

    /**
     * Get string with frontend validation classes for attribute
     *
     * @param string $attributeCode
     * @return string
     */
    public function getAttributeValidationClass($attributeCode)
    {
        return $this->_addressHelper->getAttributeValidationClass($attributeCode);
    }
}
