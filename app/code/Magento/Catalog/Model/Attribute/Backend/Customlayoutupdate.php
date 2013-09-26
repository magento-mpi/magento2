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

namespace Magento\Catalog\Model\Attribute\Backend;

class Customlayoutupdate extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @var \Magento\Adminhtml\Model\LayoutUpdate\Validator
     */
    protected $_layoutValidator;

    /**
     * @param \Magento\Adminhtml\Model\LayoutUpdate\Validator $validator
     */
    public function __construct(
        \Magento\Adminhtml\Model\LayoutUpdate\Validator $validator
    ) {
        $this->_layoutValidator = $validator;
    }

   /**
    * Product custom layout update attribute validate function.
    * In case invalid data throws exception.
    *
    * @param \Magento\Object $object
    * @throws \Magento\Eav\Model\Entity\Attribute\Exception
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
            $eavExc = new \Magento\Eav\Model\Entity\Attribute\Exception($massage);
            $eavExc->setAttributeCode($attributeName);
            throw $eavExc;
        }
        return true;
    }
}
