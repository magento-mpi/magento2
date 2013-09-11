<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Entity/Attribute/Model - attribute backend default
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Model\Entity\Attribute\Backend\Time;

class Updated extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Set modified date
     *
     * @param \Magento\Object $object
     * @return \Magento\Eav\Model\Entity\Attribute\Backend\Time\Updated
     */
    public function beforeSave($object)
    {
        $object->setData($this->getAttribute()->getAttributeCode(), \Magento\Date::now());
        return $this;
    }
}
