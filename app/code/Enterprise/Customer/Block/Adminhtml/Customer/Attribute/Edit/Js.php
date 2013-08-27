<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer and Customer Address Attributes Edit JavaScript Block
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Customer_Block_Adminhtml_Customer_Attribute_Edit_Js
    extends Magento_Adminhtml_Block_Template
{
    /**
     * Customer data
     *
     * @var Enterprise_Customer_Helper_Data
     */
    protected $_customerData = null;

    /**
     * @param Enterprise_Customer_Helper_Data $customerData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_Customer_Helper_Data $customerData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_customerData = $customerData;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve allowed Input Validate Filters in JSON format
     *
     * @return string
     */
    public function getValidateFiltersJson()
    {
        return $this->_coreData->jsonEncode
            ($this->_customerData->getAttributeValidateFilters()
        );
    }

    /**
     * Retrieve allowed Input Filter Types in JSON format
     *
     * @return string
     */
    public function getFilteTypesJson()
    {
        return $this->_coreData->jsonEncode(
            $this->_customerData->getAttributeFilterTypes()
        );
    }

    /**
     * Returns array of input types with type properties
     *
     * @return array
     */
    public function getAttributeInputTypes()
    {
        return $this->_customerData->getAttributeInputTypes();
    }
}
