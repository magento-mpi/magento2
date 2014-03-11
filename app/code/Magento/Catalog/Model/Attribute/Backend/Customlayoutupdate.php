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

use Magento\Core\Model\Layout\Update\ValidatorFactory;
use Magento\Eav\Model\Entity\Attribute\Exception;

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
     * @var ValidatorFactory
     */
    protected $_layoutUpdateValidatorFactory;

    /**
     * Construct the custom layout update class
     *
     * @param \Magento\Logger $logger
     * @param ValidatorFactory $layoutUpdateValidatorFactory
     */
    public function __construct(
        \Magento\Logger $logger,
        ValidatorFactory $layoutUpdateValidatorFactory
    ) {
        $this->_layoutUpdateValidatorFactory = $layoutUpdateValidatorFactory;
        parent::__construct($logger);
    }

    /**
     * Validate the custom layout update
     *
     * @param \Magento\Object $object
     * @return bool
     * @throws Exception
     */
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
            $eavExc = new Exception($massage);
            $eavExc->setAttributeCode($attributeName);
            throw $eavExc;
        }
        return true;
    }
}
