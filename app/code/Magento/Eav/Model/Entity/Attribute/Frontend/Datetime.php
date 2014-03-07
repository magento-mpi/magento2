<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model\Entity\Attribute\Frontend;

class Datetime extends \Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend
{
    /**
     * @var \Magento\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @param \Magento\Eav\Model\Entity\Attribute\Source\BooleanFactory $attrBooleanFactory
     * @param \Magento\Stdlib\DateTime\TimezoneInterface $localeDate
     */
    function __construct(
        \Magento\Eav\Model\Entity\Attribute\Source\BooleanFactory $attrBooleanFactory,
        \Magento\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {
        parent::__construct($attrBooleanFactory);
        $this->_localeDate = $localeDate;
    }

    /**
     * Retrieve attribute value
     *
     * @param \Magento\Object $object
     * @return mixed
     */
    public function getValue(\Magento\Object $object)
    {
        $data = '';
        $value = parent::getValue($object);
        $format = $this->_localeDate->getDateFormat(
            \Magento\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_MEDIUM
        );

        if ($value) {
            try {
                $data = $this->_localeDate->date($value, \Zend_Date::ISO_8601, null, false)->toString($format);
            } catch (\Exception $e) {
                $data = $this->_localeDate->date($value, null, null, false)->toString($format);
            }
        }

        return $data;
    }
}

