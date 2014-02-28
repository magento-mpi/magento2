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
     * @var \Magento\LocaleInterface
     */
    protected $_locale;

    /**
     * @param \Magento\Eav\Model\Entity\Attribute\Source\BooleanFactory $attrBooleanFactory
     * @param \Magento\LocaleInterface $locale
     */
    function __construct(
        \Magento\Eav\Model\Entity\Attribute\Source\BooleanFactory $attrBooleanFactory,
        \Magento\LocaleInterface $locale
    ) {
        parent::__construct($attrBooleanFactory);
        $this->_locale = $locale;
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
        $format = $this->_locale->getDateFormat(
            \Magento\LocaleInterface::FORMAT_TYPE_MEDIUM
        );

        if ($value) {
            try {
                $data = $this->_locale->date($value, \Zend_Date::ISO_8601, null, false)->toString($format);
            } catch (\Exception $e) {
                $data = $this->_locale->date($value, null, null, false)->toString($format);
            }
        }

        return $data;
    }
}

