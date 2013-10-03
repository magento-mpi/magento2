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

namespace Magento\Catalog\Model\Attribute\Backend;

class Customlayoutupdate extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{

   /**
    * Product custom layout update attribute validate function.
    * In case invalid data throws exception.
    *
    * @param \Magento\Object $object
    * @throws \Magento\Eav\Model\Entity\Attribute\Exception
    */
    /**
     * Layoutupdate validator factory
     *
     * @var \Magento\Adminhtml\Model\LayoutUpdate\ValidatorFactory
     */
    protected $_layoutUpdateValidatorFactory;

    /**
     * Construct
     *
     * @param \Magento\Adminhtml\Model\LayoutUpdate\ValidatorFactory $layoutUpdateValidatorFactory
     * @param \Magento\Core\Model\Logger $logger
     */
    public function __construct(
        \Magento\Adminhtml\Model\LayoutUpdate\ValidatorFactory $layoutUpdateValidatorFactory,
        \Magento\Core\Model\Logger $logger
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

        /** @var $validator \Magento\Adminhtml\Model\LayoutUpdate\Validator */
        $validator = $this->_layoutUpdateValidatorFactory->create();
        if (!$validator->isValid($xml)) {
            $messages = $validator->getMessages();
            //Add first message to exception
            $massage = array_shift($messages);
            $eavExc = new \Magento\Eav\Model\Entity\Attribute\Exception($massage);
            $eavExc->setAttributeCode($attributeName);
            throw $eavExc;
        }
        return true;
    }
}
