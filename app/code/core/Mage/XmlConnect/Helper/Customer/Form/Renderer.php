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
 * Customer form renderer helper
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Helper_Customer_Form_Renderer extends Mage_Core_Helper_Abstract
{
    /**
     * Get title and required attributes for a field
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Abstract $fieldsetXmlObj
     * @param Enterprise_Eav_Block_Form_Renderer_Abstract $blockObject
     * @return array
     */
    public function addTitleAndRequiredAttr(Mage_XmlConnect_Model_Simplexml_Form_Abstract $fieldsetXmlObj,
        Enterprise_Eav_Block_Form_Renderer_Abstract $blockObject
    ) {
        $attributes = array();

        if ($blockObject->isRequired()) {
            $attributes += $fieldsetXmlObj->checkAttribute('required', (int)$blockObject->isRequired());
        }

        if ($blockObject->getAdditionalDescription()) {
            $attributes += $fieldsetXmlObj->checkAttribute('title', $blockObject->getAdditionalDescription());
        }

        return $attributes;
    }
}
