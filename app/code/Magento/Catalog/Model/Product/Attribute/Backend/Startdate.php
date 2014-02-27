<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\Attribute\Backend;

/**
 *
 * Speical Start Date attribute backend
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Startdate extends \Magento\Eav\Model\Entity\Attribute\Backend\Datetime
{
    /**
     * Date model
     *
     * @var \Magento\Core\Model\Date
     */
    protected $_date;

    /**
     * Constructor
     *
     * @param \Magento\Logger $logger
     * @param \Magento\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Core\Model\Date $date
     */
    public function __construct(
        \Magento\Logger $logger,
        \Magento\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Core\Model\Date $date
    ) {
        $this->_date = $date;
        parent::__construct($logger, $localeDate);
    }

    /**
     * Get attribute value for save.
     *
     * @param \Magento\Object $object
     * @return string|bool
     */
    protected function _getValueForSave($object)
    {
        $attributeName  = $this->getAttribute()->getName();
        $startDate      = $object->getData($attributeName);
        if ($startDate === false) {
            return false;
        }
        if ($startDate == '' && $object->getSpecialPrice()) {
            $startDate = $this->_localeDate->date();
        }

        return $startDate;
    }

    /**
     * Before save hook.
     * Prepare attribute value for save
     *
     * @param \Magento\Object $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $startDate = $this->_getValueForSave($object);
        if ($startDate === false) {
            return $this;
        }

        $object->setData($this->getAttribute()->getName(), $startDate);
        parent::beforeSave($object);
        return $this;
    }

    /**
     * Product from date attribute validate function.
     * In case invalid data throws exception.
     *
     * @param \Magento\Object $object
     * @throws \Magento\Eav\Model\Entity\Attribute\Exception
     * @return bool
     */
    public function validate($object)
    {
        $attr      = $this->getAttribute();
        $maxDate   = $attr->getMaxValue();
        $startDate = $this->_getValueForSave($object);
        if ($startDate === false) {
            return true;
        }

        if ($maxDate) {
            $date     = $this->_date;
            $value    = $date->timestamp($startDate);
            $maxValue = $date->timestamp($maxDate);

            if ($value > $maxValue) {
                $message = __('The From Date value should be less than or equal to the To Date value.');
                $eavExc  = new \Magento\Eav\Model\Entity\Attribute\Exception($message);
                $eavExc->setAttributeCode($attr->getName());
                throw $eavExc;
            }
        }
        return true;
    }
}
