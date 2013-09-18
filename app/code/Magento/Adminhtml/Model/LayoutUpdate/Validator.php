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
    const XML_INVALID = 'invalidXml';
    const HELPER_ARGUMENT_TYPE = 'helperArgumentType';
    const UPDATER_MODEL = 'updaterModel';

    const XML_NAMESPACE_XSI = 'http://www.w3.org/2001/XMLSchema-instance';

    const LAYOUT_SCHEMA_SINGLE_HANDLE = 'layout_single';
    const LAYOUT_SCHEMA_MERGED = 'layout_merged';

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
        self::HELPER_ARGUMENT_TYPE => '//*[@xsi:type="helper"]',
        self::UPDATER_MODEL => '//updater',
    );

    /**
     * XSD Schemas for Layout Update validation
     *
     * @var array
     */
    protected $_xsdSchemas;

    /**
     * @var Magento_Core_Model_Config_Modules_Reader
     */
    protected $_modulesReader;

    /**
     * @var Magento_Config_DomFactory
     */
    protected $_domConfigFactory;

    /**
     * @param Magento_Core_Model_Config_Modules_Reader $modulesReader
     * @param Magento_Config_DomFactory $domConfigFactory
     */
    public function __construct(
        Magento_Core_Model_Config_Modules_Reader $modulesReader,
        Magento_Config_DomFactory $domConfigFactory
    ) {
        $this->_modulesReader = $modulesReader;
        $this->_domConfigFactory = $domConfigFactory;
        $this->_initMessageTemplates();
        $this->_xsdSchemas = array(
            self::LAYOUT_SCHEMA_SINGLE_HANDLE => $this->_modulesReader->getModuleDir('etc', 'Magento_Core')
                . DIRECTORY_SEPARATOR . 'layout_single.xsd',
            self::LAYOUT_SCHEMA_MERGED => $this->_modulesReader->getModuleDir('etc', 'Magento_Core')
                . DIRECTORY_SEPARATOR . 'layout_merged.xsd',
        );
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
                self::HELPER_ARGUMENT_TYPE =>
                    __('Helper arguments should not be used in custom layout updates.'),
                self::UPDATER_MODEL =>
                    __('Updater model should not be used in custom layout updates.'),
                self::XML_INVALID => __('Please correct the XML data and try again. %value%'),
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
     * @param string $schema
     * @param boolean $isSecurityCheck
     * @return bool
     */
    public function isValid($value, $schema = self::LAYOUT_SCHEMA_SINGLE_HANDLE, $isSecurityCheck = true)
    {
        try {
            //wrap XML value in the "layout" and "handle" tags to make it validatable
            $value = '<layout xmlns:xsi="' . self::XML_NAMESPACE_XSI . '">' . $value . '</layout>';
            $this->_domConfigFactory->createDom(array(
                'xml' => $value,
                'schemaFile' => $this->_xsdSchemas[$schema]
            ));

            if ($isSecurityCheck) {
                $value = new Magento_Simplexml_Element($value);
                $value->registerXPathNamespace('xsi', self::XML_NAMESPACE_XSI);
                foreach ($this->_protectedExpressions as $key => $xpr) {
                    if ($value->xpath($xpr)) {
                        $this->_error($key);
                    }
                }
                $errors = $this->getMessages();
                if (!empty($errors)) {
                    return false;
                }
            }
        } catch (Magento_Config_Dom_ValidationException $e) {
            $this->_error(self::XML_INVALID, $e->getMessage());
            return false;
        } catch (Exception $e) {
            var_dump($e->getMessage());die;
            $this->_error(self::XML_INVALID);
            return false;
        }
        return true;
    }
}
