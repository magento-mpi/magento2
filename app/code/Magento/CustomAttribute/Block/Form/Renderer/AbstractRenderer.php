<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomAttribute
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * EAV entity Attribute Form Renderer Abstract Block
 *
 * @category    Magento
 * @package     Magento_CustomAttribute
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomAttribute\Block\Form\Renderer;

abstract class AbstractRenderer extends \Magento\Core\Block\Template
{
    /**
     * Attribute instance
     *
     * @var \Magento\Eav\Model\Attribute
     */
    protected $_attribute;

    /**
     * EAV Entity Model
     *
     * @var \Magento\Core\Model\AbstractModel
     */
    protected $_entity;

    /**
     * Format for HTML elements id attribute
     *
     * @var string
     */
    protected $_fieldIdFormat   = '%1$s';

    /**
     * Format for HTML elements name attribute
     *
     * @var string
     */
    protected $_fieldNameFormat = '%1$s';

    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_locale = $locale;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Set attribute instance
     *
     * @param \Magento\Eav\Model\Attribute $attribute
     * @return \Magento\CustomAttribute\Block\Form\Renderer\AbstractRenderer
     */
    public function setAttributeObject(\Magento\Eav\Model\Attribute $attribute)
    {
        $this->_attribute = $attribute;
        return $this;
    }

    /**
     * Return Attribute instance
     *
     * @return \Magento\Eav\Model\Attribute
     */
    public function getAttributeObject()
    {
        return $this->_attribute;
    }

    /**
     * Set Entity object
     *
     * @param \Magento\Core\Model\AbstractModel
     * @return \Magento\CustomAttribute\Block\Form\Renderer\AbstractRenderer
     */
    public function setEntity(\Magento\Core\Model\AbstractModel $entity)
    {
        $this->_entity = $entity;
        return $this;
    }

    /**
     * Return Entity object
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    /**
     * Return Data Form Filter or false
     *
     * @return \Magento\Data\Form\Filter\FilterInterface
     */
    protected function _getFormFilter()
    {
        $filterCode = $this->getAttributeObject()->getInputFilter();
        if ($filterCode) {
            $filterClass = 'Magento\\Data\\Form\\Filter\\' . ucfirst($filterCode);
            if ($filterCode == 'date') {
                $format = $this->_locale->getDateFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT);
                $filter = new $filterClass($format);
            } else {
                $filter = new $filterClass();
            }
            return $filter;
        }
        return false;
    }

    /**
     * Apply output filter to value
     *
     * @param string $value
     * @return string
     */
    protected function _applyOutputFilter($value)
    {
        $filter = $this->_getFormFilter();
        if ($filter) {
            $value = $filter->outputFilter($value);
        }

        return $value;
    }

    /**
     * Return validate class by attribute input validation rule
     *
     * @return string|false
     */
    protected function _getInputValidateClass()
    {
        $class          = false;
        $validateRules  = $this->getAttributeObject()->getValidateRules();
        if (!empty($validateRules['input_validation'])) {
            switch ($validateRules['input_validation']) {
                case 'alphanumeric':
                    $class = 'validate-alphanum';
                    break;
                case 'numeric':
                    $class = 'validate-digits';
                    break;
                case 'alpha':
                    $class = 'validate-alpha';
                    break;
                case 'email':
                    $class = 'validate-email';
                    break;
                case 'url':
                    $class = 'validate-url';
                    break;
                case 'date':
                    // @todo DATE FORMAT
                    break;
            }
        }
        return $class;
    }

    /**
     * Return array of validate classes
     *
     * @param boolean $withRequired
     * @return array
     */
    protected function _getValidateClasses($withRequired = true)
    {
        $classes = array();
        if ($withRequired && $this->isRequired()) {
            $classes[] = 'required-entry';
        }
        $inputRuleClass = $this->_getInputValidateClass();
        if ($inputRuleClass) {
            $classes[] = $inputRuleClass;
        }
        return $classes;
    }

    /**
     * Return original entity value
     * Value didn't escape and filter
     *
     * @return string
     */
    public function getValue()
    {
        $value = $this->getEntity()->getData($this->getAttributeObject()->getAttributeCode());
        return $value;
    }

    /**
     * Return HTML id for element
     *
     * @param string|null $index
     * @return string
     */
    public function getHtmlId($index = null)
    {
        $format = $this->_fieldIdFormat;
        if (!is_null($index)) {
            $format .= '_%2$s';
        }
        return sprintf($format, $this->getAttributeObject()->getAttributeCode(), $index);
    }

    /**
     * Return HTML id for element
     *
     * @param string|null $index
     * @return string
     */
    public function getFieldName($index = null)
    {
        $format = $this->_fieldNameFormat;
        if (!is_null($index)) {
            $format .= '[%2$s]';
        }
        return sprintf($format, $this->getAttributeObject()->getAttributeCode(), $index);
    }

    /**
     * Return HTML class attribute value
     * Validate and rules
     *
     * @return string
     */
    public function getHtmlClass()
    {
        $classes = $this->_getValidateClasses(true);
        return empty($classes) ? '' : ' ' . implode(' ', $classes);
    }

    /**
     * Check is attribute value required
     *
     * @return boolean
     */
    public function isRequired()
    {
        return $this->getAttributeObject()->getIsRequired();
    }

    /**
     * Return attribute label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->getAttributeObject()->getStoreLabel();
    }

    /**
     * Set format for HTML element(s) id attribute
     *
     * @param string $format
     * @return \Magento\CustomAttribute\Block\Form\Renderer\AbstractRenderer
     */
    public function setFieldIdFormat($format)
    {
        $this->_fieldIdFormat = $format;
        return $this;
    }

    /**
     * Set format for HTML element(s) name attribute
     *
     * @param string $format
     * @return \Magento\CustomAttribute\Block\Form\Renderer\AbstractRenderer
     */
    public function setFieldNameFormat($format)
    {
        $this->_fieldNameFormat = $format;
        return $this;
    }
}
