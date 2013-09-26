<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product url key attribute backend
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Catalog_Model_Attribute_Backend_Customlayoutupdate extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * @var Magento_Adminhtml_Model_LayoutUpdate_Validator
     */
    protected $_layoutValidator;

    /**
     * @param Magento_Adminhtml_Model_LayoutUpdate_Validator $validator
     */
    public function __construct(
        Magento_Adminhtml_Model_LayoutUpdate_Validator $validator
    ) {
        $this->_layoutValidator = $validator;
    }

   /**
    * Product custom layout update attribute validate function.
    * In case invalid data throws exception.
    *
    * @param Magento_Object $object
    * @throws Magento_Eav_Model_Entity_Attribute_Exception
    */
    public function validate($object)
    {
        $attributeName = $this->getAttribute()->getName();
        $xml = trim($object->getData($attributeName));

        if (!$this->getAttribute()->getIsRequired() && empty($xml)) {
            return true;
        }

        if (!$this->_layoutValidator->isValid($xml)) {
            $messages = $this->_layoutValidator->getMessages();
            //Add first message to exception
            $massage = array_shift($messages);
            $eavExc = new Magento_Eav_Model_Entity_Attribute_Exception($massage);
            $eavExc->setAttributeCode($attributeName);
            throw $eavExc;
        }
        return true;
    }
}
