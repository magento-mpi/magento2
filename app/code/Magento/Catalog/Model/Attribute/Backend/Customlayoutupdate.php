<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Attribute\Backend;

/**
 * Product url key attribute backend
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Customlayoutupdate extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{

    /**
     * Layout update validator factory
     *
     * @var \Magento\Core\Model\Layout\Update\ValidatorFactory
     */
    protected $_layoutUpdateValidatorFactory;

    /**
     * @param \Magento\Logger $logger
     * @param \Magento\Core\Model\Layout\Update\ValidatorFactory $layoutUpdateValidatorFactory
     */
    public function __construct(
        \Magento\Logger $logger,
        \Magento\Core\Model\Layout\Update\ValidatorFactory $layoutUpdateValidatorFactory
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

        /** @var $validator \Magento\Core\Model\Layout\Update\Validator */
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
