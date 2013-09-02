<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Rma_Block_Email_Items extends Magento_Rma_Block_Form
{
    /**
     * Variable to store store-depended string values of attributes
     *
     * @var null|array
     */
    protected $_attributeOptionValues = null;

    /**
     * Rma eav
     *
     * @var Magento_Rma_Helper_Eav
     */
    protected $_rmaEav = null;

    /**
     * @param Magento_Rma_Helper_Eav $rmaEav
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Rma_Helper_Eav $rmaEav,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_rmaEav = $rmaEav;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get string label of option-type item attributes
     *
     * @param int $attributeValue
     * @return string
     */
    public function getOptionAttributeStringValue($attributeValue)
    {
        if (is_null($this->_attributeOptionValues)) {
            $this->_attributeOptionValues = $this->_rmaEav->getAttributeOptionStringValues();
        }
        if (isset($this->_attributeOptionValues[$attributeValue])) {
            return $this->_attributeOptionValues[$attributeValue];
        } else {
            return '';
        }
    }
}
