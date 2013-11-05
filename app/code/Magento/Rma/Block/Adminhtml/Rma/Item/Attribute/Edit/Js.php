<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * RMA Items Attributes Edit JavaScript Block
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Item\Attribute\Edit;

class Js
    extends \Magento\Backend\Block\Template
{
    /**
     * Rma eav
     *
     * @var \Magento\CustomAttribute\Helper\Data
     */
    protected $_attributeHelper = null;

    /**
     * @param \Magento\CustomAttribute\Helper\Data $attributeHelper
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\CustomAttribute\Helper\Data $attributeHelper,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_attributeHelper = $attributeHelper;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve allowed Input Validate Filters in JSON format
     *
     * @return string
     */
    public function getValidateFiltersJson()
    {
        return $this->_coreData->jsonEncode(
            $this->_attributeHelper->getAttributeValidateFilters()
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
            $this->_attributeHelper->getAttributeFilterTypes()
        );
    }

    /**
     * Returns array of input types with type properties
     *
     * @return array
     */
    public function getAttributeInputTypes()
    {
        return $this->_attributeHelper->getAttributeInputTypes();
    }
}
