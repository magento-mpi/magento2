<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Validator for custom layout update
 *
 * Validator checked XML validation and protected expressions
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_Layoutupdate_Validator_Layoutupdate extends Zend_Validate_Abstract
{
    const XML_INVALID                             = 'invalidXml';
    const PROTECTED_ATTR_HELPER_IN_TAG_ACTION_VAR = 'protectedAttrHelperInActionVar';

    /**
     * The Varien SimpleXml object
     *
     * @var Varien_Simplexml_Element
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
     * Helper
     *
     * @var Mage_Catalog_Helper_Data
     */
    protected $_helper;

    /**
     * Helper class path
     *
     * @var string
     */
    protected $_helperClassPath = 'core';

    /**
     * Construct
     */
    public function __construct()
    {
        $this->_initErrorMessages();
    }

    /**
     * Initialize error messages with translating
     *
     * @return Mage_Adminhtml_Model_Layoutupdate_Validator_Layoutupdate
     */
    protected function _initErrorMessages()
    {
        if (!$this->_messageTemplates) {
            $this->_messageTemplates = array(
                self::PROTECTED_ATTR_HELPER_IN_TAG_ACTION_VAR =>
                    $this->_getHelper()->__('In XML data, the "action" tag cannot contain the "helper" attribute.'),
                self::XML_INVALID => $this->_getHelper()->__('XML data is invalid.'),
            );
        }
        return $this;
    }

    /**
     * Check on the validity
     *
     * @throws Mage_Core_Exception  Throw exception when xml object is not instance of Varien_Simplexml_Element
     * @param Varien_Simplexml_Element|string $value
     * @return bool
     */
    public function isValid($value)
    {
        if (is_string($value)) {
            $value = trim($value);
            try {
                $value = new Varien_Simplexml_Element($value);
            } catch(Exception $e) {
                $this->_error(self::XML_INVALID);
                return false;
            }
        } elseif (!($value instanceof Varien_Simplexml_Element)) {
            throw new Mage_Core_Exception(
                $this->_getHelper()->__('XML object is not instance of "Varien_Simplexml_Element".'));
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

    /**
     * Get helper
     *
     * @return Mage_Core_Helper_Data
     */
    protected function _getHelper()
    {
        if (null === $this->_helper) {
            $this->_helper = Mage::helper($this->_helperClassPath);
        }
        return $this->_helper;
    }
}
