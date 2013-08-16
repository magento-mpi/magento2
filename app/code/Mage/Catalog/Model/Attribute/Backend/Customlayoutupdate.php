<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product url key attribute backend
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Catalog_Model_Attribute_Backend_Customlayoutupdate extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{

   /**
    * Product custom layout update attribute validate function.
    * In case invalid data throws exception.
    *
    * @param Magento_Object $object
    * @throws Mage_Eav_Model_Entity_Attribute_Exception
    */
    public function validate($object)
    {
        $attributeName = $this->getAttribute()->getName();
        $xml = trim($object->getData($attributeName));

        if (!$this->getAttribute()->getIsRequired() && empty($xml)) {
            return true;
        }

        /** @var $validator Mage_Adminhtml_Model_LayoutUpdate_Validator */
        $validator = Mage::getModel('Mage_Adminhtml_Model_LayoutUpdate_Validator');
        if (!$validator->isValid($xml)) {
            $messages = $validator->getMessages();
            //Add first message to exception
            $massage = array_shift($messages);
            $eavExc = new Mage_Eav_Model_Entity_Attribute_Exception($massage);
            $eavExc->setAttributeCode($attributeName);
            throw $eavExc;
        }
        return true;
    }
}
