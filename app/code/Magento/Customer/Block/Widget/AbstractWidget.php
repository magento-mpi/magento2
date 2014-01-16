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
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @var \Magento\Customer\Helper\Address
     */
    protected $_addressHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Customer\Helper\Address $addressHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Helper\Address $addressHelper,
        array $data = array()
    ) {
        $this->_addressHelper = $addressHelper;
        $this->_eavConfig = $eavConfig;
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
     * @return \Magento\Customer\Model\Attribute|false
     */
    protected function _getAttribute($attributeCode)
    {
        return $this->_eavConfig->getAttribute('customer', $attributeCode);
    }
}
