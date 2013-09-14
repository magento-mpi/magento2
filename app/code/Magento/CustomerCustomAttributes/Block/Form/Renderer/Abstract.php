<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer Attribute Form Renderer Abstract Block
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_CustomerCustomAttributes_Block_Form_Renderer_Abstract extends Magento_CustomAttribute_Block_Form_Renderer_Abstract
{
    /**
     * @var Magento_Eav_Model_AttributeDataFactory
     */
    protected $_attrDataFactory;

    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Eav_Model_AttributeDataFactory $attrDataFactory,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_attrDataFactory = $attrDataFactory;
    }

    /**
     * Get additional description message for attribute field
     *
     * @return boolean|string
     */
    public function getAdditionalDescription()
    {
        $result = false;
        if ($this->isRequired() &&
            $this->getEntity()->getId() &&
            $this->getEntity()->validate() === true &&
            $this->validateValue($this->getValue()) !== true) {
                $result = __('Edit this attribute here to use in an address template.');
            }

        return $result;
    }

    /**
     * Validate attribute value
     *
     * @param array|string $value
     * @throws Magento_Core_Exception
     * @return boolean
     */
    public function validateValue($value)
    {
        $dataModel = $this->_attrDataFactory->create($this->getAttributeObject(), $this->getEntity());
        $result = $dataModel->validateValue($this->getValue());
        return $result;
    }
}
