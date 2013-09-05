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
     * @var \Magento\Simplexml\Element
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
     * @var Magento_Core_Model_Config_Modules_Reader
     */
    protected $_modulesReader;

    /**
     * @var \Magento\Config\DomFactory
     */
    protected $_domConfigFactory;

    /**
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param \Magento\Config\DomFactory $domConfigFactory
     */
    public function __construct(
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        \Magento\Config\DomFactory $domConfigFactory
    ) {
        $this->_modulesReader = $modulesReader;
        $this->_domConfigFactory = $domConfigFactory;
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
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {
        try {
            //wrap XML value in the "layout" and "handle" tags to make it validatable
            $value = '<layout xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
                . '<handle id="handleId">' . trim($value) . '</handle>'
                . '</layout>';

            $schema = $this->_modulesReader->getModuleDir('etc', 'Magento_Core') . DIRECTORY_SEPARATOR . 'layouts.xsd';
            $dom = $this->_domConfigFactory->createDom(array(
                'xml' => $value,
                'schemaFile' => $schema
            ));
            $value = new \Magento\Simplexml\Element($value);
        } catch (Exception $e) {
            $this->_error(self::XML_INVALID);
            return false;
        }

        foreach ($this->_protectedExpressions as $key => $xpr) {
            if ($value->xpath($xpr)) {
                $this->_error($key);
                return false;
            }
        }
        return true;
    }
}
