<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer image file field form xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Form_Renderer_Image extends Mage_XmlConnect_Block_Customer_Form_Renderer_File
{
    /**
     * Field type
     *
     * @var string
     */
    protected $_filedType = 'image';

    /**
     * Add validator for image file field to fieldset xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Abstract $fieldXmlObj
     * @return Mage_XmlConnect_Block_Customer_Form_Renderer_Image
     */
    protected function _addValidator(Mage_XmlConnect_Model_Simplexml_Form_Element_Abstract $fieldXmlObj)
    {
        parent::_addValidator($fieldXmlObj);

        $validateRules = $this->getAttributeObject()->getValidateRules();

        if (!empty($validateRules)) {

            foreach ($fieldXmlObj->getElements() as $element) {
                if ($element->getType() == 'validator') {
                    $validatorXmlObj = $element;
                }
            }

            if (!isset($validatorXmlObj)) {
                $validatorXmlObj = $fieldXmlObj->addValidator();
            }

            if (!empty($validateRules['max_image_width'])) {
                $minTextLength = (int) $validateRules['max_image_width'];
                $validatorXmlObj->addRule(array(
                    'type' => 'max_image_width', 'value' => $minTextLength, 'field_label' => $this->getLabel()
                ));
            }

            if (!empty($validateRules['max_image_heght'])) {
                $maxTextLength = $validateRules['max_image_heght'];
                $validatorXmlObj->addRule(array(
                    'type' => 'max_image_height', 'value' => $maxTextLength, 'field_label' => $this->getLabel()
                ));
            }
        }
        return $this;
    }
}
