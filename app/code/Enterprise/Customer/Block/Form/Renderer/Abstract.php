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
 * Customer Attribute Form Renderer Abstract Block
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Enterprise_Customer_Block_Form_Renderer_Abstract extends Enterprise_Eav_Block_Form_Renderer_Abstract
{

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
        $dataModel = Magento_Customer_Model_Attribute_Data::factory($this->getAttributeObject(), $this->getEntity());
        $result = $dataModel->validateValue($this->getValue());
        return $result;
    }
}
