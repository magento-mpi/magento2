<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Validator for custom layout update
 *
 * Validator checked XML validation and protected expressions
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Model_LayoutUpdate_Validator extends Zend_Validate_Abstract
{
    const XML_INVALID                             = 'invalidXml';
    const PROTECTED_ATTR_HELPER_IN_TAG_ACTION_VAR = 'protectedAttrHelperInActionVar';

    /**
     * The Magento SimpleXml object
     *
     * @var Magento_Simplexml_Element
     */
    protected $_value;

    /**
     * Protected expressions
     *
     * @var array
     */
    protected $_protectedExpressions = array(
        self::PROTECTED_ATTR_HELPER_IN_TAG_ACTION_VAR => '//action/*[@helper]',
    );

    /**
     * Construct
     */
    public function __construct()
    {
        $this->_initMessageTemplates();
    }

    /**
     * Initialize messages templates with translating
     *
     * @return Magento_Adminhtml_Model_LayoutUpdate_Validator
     */
    protected function _initMessageTemplates()
    {
        if (!$this->_messageTemplates) {
            $this->_messageTemplates = array(
                self::PROTECTED_ATTR_HELPER_IN_TAG_ACTION_VAR =>
                    __('Helper attributes should not be used in custom layout updates.'),
                self::XML_INVALID => __('Please correct the XML data and try again.'),
            );
        }
        return $this;
    }

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @throws Exception            Throw exception when xml object is not
     *                              instance of Magento_Simplexml_Element
     * @param Magento_Simplexml_Element|string $value
     * @return bool
     */
    public function isValid($value)
    {
        if (is_string($value)) {
            $value = trim($value);
            try {
                //wrap XML value in the "config" tag because config cannot
                //contain multiple root tags
                $value = new Magento_Simplexml_Element('<config>' . $value . '</config>');
            } catch (Exception $e) {
                $this->_error(self::XML_INVALID);
                return false;
            }
        } elseif (!($value instanceof Magento_Simplexml_Element)) {
            throw new Exception(
                __('XML object is not instance of "Magento_Simplexml_Element".'));
        }

        $this->_setValue($value);

        foreach ($this->_protectedExpressions as $key => $xpr) {
            if ($this->_value->xpath($xpr)) {
                $this->_error($key);
                return false;
            }
        }
        return true;
    }
}
