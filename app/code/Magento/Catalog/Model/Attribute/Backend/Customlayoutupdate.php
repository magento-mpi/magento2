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
 * @SuppressWarnings(PHPMD.LongVariable)
 */

class Magento_Catalog_Model_Attribute_Backend_Customlayoutupdate extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{

   /**
    * Product custom layout update attribute validate function.
    * In case invalid data throws exception.
    *
    * @param Magento_Object $object
    * @throws Magento_Eav_Model_Entity_Attribute_Exception
    */
    /**
     * Layoutupdate validator factory
     *
     * @var Magento_Adminhtml_Model_LayoutUpdate_ValidatorFactory
     */
    protected $_layoutUpdateValidatorFactory;

    /**
     * Construct
     *
     * @param Magento_Adminhtml_Model_LayoutUpdate_ValidatorFactory $layoutUpdateValidatorFactory
     * @param Magento_Core_Model_Logger $logger
     */
    public function __construct(
        Magento_Adminhtml_Model_LayoutUpdate_ValidatorFactory $layoutUpdateValidatorFactory,
        Magento_Core_Model_Logger $logger
    ) {
        $this->_layoutUpdateValidatorFactory = $layoutUpdateValidatorFactory;
        parent::__construct($logger);
    }

    public function validate($object)
    {
        $attributeName = $this->getAttribute()->getName();
        $xml = trim($object->getData($attributeName));

        if (!$this->getAttribute()->getIsRequired() && empty($xml)) {
            return true;
        }

        /** @var $validator Magento_Adminhtml_Model_LayoutUpdate_Validator */
        $validator = $this->_layoutUpdateValidatorFactory->create();
        if (!$validator->isValid($xml)) {
            $messages = $validator->getMessages();
            //Add first message to exception
            $massage = array_shift($messages);
            $eavExc = new Magento_Eav_Model_Entity_Attribute_Exception($massage);
            $eavExc->setAttributeCode($attributeName);
            throw $eavExc;
        }
        return true;
    }
}
