<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * RMA Items Attributes Edit JavaScript Block
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Adminhtml_Rma_Item_Attribute_Edit_Js
    extends Magento_Backend_Block_Template
{
    /**
     * Rma eav
     *
     * @var Enterprise_Rma_Helper_Eav
     */
    protected $_rmaEav = null;

    /**
     * @param Enterprise_Rma_Helper_Eav $rmaEav
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_Rma_Helper_Eav $rmaEav,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_rmaEav = $rmaEav;
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
            $this->_rmaEav->getAttributeValidateFilters()
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
            $this->_rmaEav->getAttributeFilterTypes()
        );
    }

    /**
     * Returns array of input types with type properties
     *
     * @return array
     */
    public function getAttributeInputTypes()
    {
        return $this->_rmaEav->getAttributeInputTypes();
    }
}
