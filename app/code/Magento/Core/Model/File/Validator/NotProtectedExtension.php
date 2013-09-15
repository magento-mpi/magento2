<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Validator for check not protected file extensions
 *
 * @category   Magento
 * @package    Magento_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\File\Validator;

class NotProtectedExtension extends \Zend_Validate_Abstract
{
    const PROTECTED_EXTENSION = 'protectedExtension';

    /**
     * The file extension
     *
     * @var string
     */
    protected $_value;

    /**
     * Protected file types
     *
     * @var array
     */
    protected $_protectedFileExtensions = array();

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * Construct
     *
     *
     *
     * @param Magento_Core_Helper_Data $coreData
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData
    ) {
        $this->_coreData = $coreData;
        $this->_initMessageTemplates();
        $this->_initProtectedFileExtensions();
    }

    /**
     * Initialize message templates with translating
     *
     * @return \Magento\Core\Model\File\Validator\NotProtectedExtension
     */
    protected function _initMessageTemplates()
    {
        if (!$this->_messageTemplates) {
            $this->_messageTemplates = array(
                self::PROTECTED_EXTENSION => __('File with an extension "%value%" is protected and cannot be uploaded'),
            );
        }
        return $this;
    }

    /**
     * Initialize protected file extensions
     *
     * @return \Magento\Core\Model\File\Validator\NotProtectedExtension
     */
    protected function _initProtectedFileExtensions()
    {
        if (!$this->_protectedFileExtensions) {
            /** @var $helper \Magento\Core\Helper\Data */
            $helper = $this->_coreData;
            $extensions = $helper->getProtectedFileExtensions();
            if (is_string($extensions)) {
                $extensions = explode(',', $extensions);
            }
            foreach ($extensions as &$ext) {
                $ext = strtolower(trim($ext));
            }
            $this->_protectedFileExtensions = (array) $extensions;
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
     * @param string $value         Extension of file
     * @return bool
     */
    public function isValid($value)
    {
        $value = strtolower(trim($value));
        $this->_setValue($value);

        if (in_array($this->_value, $this->_protectedFileExtensions)) {
            $this->_error(self::PROTECTED_EXTENSION, $this->_value);
            return false;
        }

        return true;
    }
}
