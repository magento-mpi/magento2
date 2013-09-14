<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Eav_Model_Entity_Attribute_Frontend_Datetime extends Magento_Eav_Model_Entity_Attribute_Frontend_Abstract
{
    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @param Magento_Eav_Model_Entity_Attribute_Source_BooleanFactory $attrBooleanFactory
     * @param Magento_Core_Model_LocaleInterface $locale
     */
    function __construct(
        Magento_Eav_Model_Entity_Attribute_Source_BooleanFactory $attrBooleanFactory,
        Magento_Core_Model_LocaleInterface $locale
    ) {
        parent::__construct($attrBooleanFactory);
        $this->_locale = $locale;
    }

    /**
     * Retreive attribute value
     *
     * @param $object
     * @return mixed
     */
    public function getValue(Magento_Object $object)
    {
        $data = '';
        $value = parent::getValue($object);
        $format = $this->_locale->getDateFormat(
            Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM
        );

        if ($value) {
            try {
                $data = $this->_locale->date($value, Zend_Date::ISO_8601, null, false)->toString($format);
            } catch (Exception $e) {
                $data = $this->_locale->date($value, null, null, false)->toString($format);
            }
        }

        return $data;
    }
}

