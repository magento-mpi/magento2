<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Eav\Model\Entity\Attribute\Backend\Time;

/**
 * Entity/Attribute/Model - attribute backend default
 */
class Created extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Logger $logger
     * @param \Magento\Stdlib\DateTime $dateTime
     */
    public function __construct(
        \Magento\Logger $logger,
        \Magento\Stdlib\DateTime $dateTime
    ) {
        $this->dateTime = $dateTime;
        parent::__construct($logger);
    }

    /**
     * Set created date
     *
     * @param \Magento\Core\Model\Object $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        if ($object->isObjectNew() && is_null($object->getData($attributeCode))) {
            $object->setData($attributeCode, $this->dateTime->now());
        }

        return $this;
    }
}
