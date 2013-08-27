<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Validate URL
 *
 * @category   Magento
 * @package    Magento_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Url_Validator extends Zend_Validate_Abstract
{
    /**#@+
     * Error keys
     */
    const INVALID_URL = 'invalidUrl';
    /**#@-*/

    /**
     * Object constructor
     */
    public function __construct()
    {
        // set translated message template
        $this->setMessage(__("Invalid URL '%value%'."), self::INVALID_URL);
    }

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID_URL => "Invalid URL '%value%'.",
    );

    /**
     * Validate value
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {
        $this->_setValue($value);

        //check valid URL
        if (!Zend_Uri::check($value)) {
            $this->_error(self::INVALID_URL);
            return false;
        }

        return true;
    }
}
